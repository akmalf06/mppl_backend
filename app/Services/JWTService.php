<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\AuthenticationException;

class JWTService
{
    public function parseUserId($token) {
        try {
            if(!$token || $token == "") throw new \Exception("Authentication failed");

            $data = JWT::decode($token, new Key(config("app.key"), "HS256"));

            return $data->sub;
        } catch (\Exception $e) {
            throw new AuthenticationException("Authentication failed");
        }
    }

    public function createToken(int $userId): string 
    {
        return JWT::encode(
            [
                'iss' => "mpplmienta",
                "iat" => time(),
                "nbf" => time(),
                "exp" => time() + 60 * 60 * 24 * 7,
                'sub' => $userId
            ], 
            config("app.key"),
            "HS256"
        );
    }
}