<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait ApiResponse
{
    /**
     * Send "OK" response.
     *
     * @param string $status
     * @param int $code
     * @return JsonResponse
     */
    public function sendOk(string $status = "OK", int $code = 200): JsonResponse
    {
        return response()->json(
            [
                "code" => $code,
                "status" => $status,
            ],
            $code,
            [],
            JSON_UNESCAPED_SLASHES,
        );
    }

    /**
     * Send data response.
     *
     * @param mixed $data
     * @param string $status
     * @param int $code
     * @return JsonResponse
     */
    public function sendData(
        mixed $data,
        string $status = "OK",
        int $code = 200,
    ): JsonResponse {
        $contents = [
            "code" => $code,
            "status" => $status,
        ];

        $data =
            $data instanceof JsonResource
                ? (array) $data->response()->getData()
                : ["data" => $data];
        $contents = array_merge($contents, $data);

        return response()->json($contents, $code, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Send error response.
     *
     * @param string|array $message
     * @param string $status
     * @param int $code
     * @param Throwable|null $exception
     *
     * @return JsonResponse
     */
    public function sendError(
        string|array $message,
        string $status = "Error",
        int $code = 400,
        Throwable|null $exception = null,
    ): JsonResponse {
        $content = [
            "code" => $code,
            "status" => $status,
            "message" => $message,
        ];

        // Show error trace if exception parameter is given.
        if ($exception) {
            $content = array_merge($content, [
                "exception" => get_class($exception),
                "file" => $exception->getFile(),
                "line" => $exception->getLine(),
                "trace" => collect($exception->getTrace())
                    ->map(static function ($trace): array {
                        return Arr::except($trace, ["args"]);
                    })
                    ->all(),
            ]);
        }

        return response()->json($content, $code, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Handle exception.
     *
     * @param Throwable $exception
     * @return JsonResponse
     */
    public function handleApiException(
        Throwable $exception,
        $request,
    ): JsonResponse {
        $request_id_code = "";
        if ($request) {
            // generating request unique id
            $mytime = Carbon::now();
            $time = $mytime->toDateTimeString();
            $request_id = $request->ip() . " " . $time;
            $request_id_code = Str::upper(Str::substr(md5($request_id), 0, 8));
        } else {
            $request_id_code = "None";
        }

        Log::error(
            date("Y-m-d H:i:s") . " " . $request_id_code . " " . $exception,
        );

        return $this->sendError(
            $this->getErrorMessage($exception, $request_id_code),
            $this->getStatusMessage($exception),
            $this->getApiErrorCode($exception),
            config("app.debug", false) ? $exception : null,
        );
    }

    /**
     * Get API error status message.
     *
     * @param Throwable $exception
     * @return string
     */
    private function getStatusMessage(Throwable $exception): string
    {
        if ($exception instanceof AuthenticationException) {
            return "Unauthenticated.";
        }

        if ($exception instanceof AuthorizationException) {
            return "Unauthorized.";
        }

        return "Error";
    }

    /**
     * Get API error message.
     *
     * @param Throwable $exception
     * @return string|array
     */
    private function getErrorMessage(
        Throwable $exception,
        $request_id,
    ): string|array {
        if ($exception instanceof ValidationException) {
            return $exception->validator->errors()->first();
        }

        if ($exception instanceof ConnectionException) {
            return "Server is busy.";
        }

        if ($exception instanceof AuthenticationException || env("APP_DEBUG")) {
            return $exception->getMessage();
        }

        if ($exception instanceof BadRequestException) {
            return $exception->getMessage();
        }

        if ($exception instanceof ModelNotFoundException) {
            return $exception->getMessage();
        }

        return "Terjadi Kesalahan. Request Id: " . $request_id;
    }

    /**
     * Get API error code.
     *
     * @param Throwable $exception
     *
     * @return int
     */
    protected function getApiErrorCode(Throwable $exception): int
    {
        if ($exception instanceof ValidationException) {
            return 422;
        }

        if ($exception instanceof BadRequestException) {
            return 400;
        }

        if ($exception instanceof ModelNotFoundException) {
            return 404;
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof AuthenticationException) {
            return 401;
        }

        if ($exception instanceof AuthorizationException) {
            return 403;
        }

        return 500;
    }
}
