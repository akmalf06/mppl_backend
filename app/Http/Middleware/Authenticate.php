<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JWTService;
use Closure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwtService = App::make(JWTService::class);

        if (
            !$jwtService instanceof JWTService
        ) {
            throw new Exception("Unknown error on authentication.");
        }

        // parse model id and model type from token
        $userId = $jwtService->parseUserId($request->bearerToken() ?? "");

        // get user model
        $user = User::find($userId);
        if (!$user) 
        {
            throw new AuthenticationException("User not found.");
        }

        $this->authorize($request->route('branchId'), $user);

        // set user model
        Auth::login($user);
        
        return $next($request);
    }

    private function authorize(int $branchId, User $user): void
    {
        if (
            $user->user_type !== User::USER_ADMIN ||
            $user->branch_id !== $branchId
        ) {
            throw new AuthorizationException(
                "Anda tidak berhak mengakses fitur ini",
            );
        }
    }
}
