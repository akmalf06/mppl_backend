<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function sendError($message, $status = 'Error', $code = 400): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }

    public function handleException(Throwable $e): JsonResponse
    {
        if ($e instanceof HttpException) {
            $code = $e->getStatusCode();
            if (!$message = $e->getMessage())
                $message = Response::$statusTexts[$code];

            return $this->sendError($message, 'Error', $code);
        }

        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));

            return $this->sendError("$model not found", 'Error', Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof AuthorizationException) {
            return $this->sendError($e->getMessage(), 'Error', Response::HTTP_FORBIDDEN);
        }

        if ($e instanceof AuthenticationException) {
            return $this->sendError($e->getMessage(), 'Error', Response::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof ValidationException) {
            $errors = $e->validator->errors()->getMessages();

            return $this->sendError($errors, 'Error',Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (config('app.debug')) {
            Log::debug($e, ['request' => request()->all()]);

            return $this->sendError($e->getMessage(), 'Error', 500);
        }

        Log::critical($e, ['request' => request()->all()]);

        return $this->sendError('Unexpected error. Try again later.', 'Error', 500);
    }
}
