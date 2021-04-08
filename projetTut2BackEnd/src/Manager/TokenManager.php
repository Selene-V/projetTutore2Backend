<?php

namespace App\Manager;

use Firebase\JWT\JWT;

class TokenManager
{
    public function encode(array $data): string
    {
        $key = file_get_contents(__DIR__ . '/../../config/keys/private.pem');

        return JWT::encode($data, $key, 'RS256');
    }

    public function decode(string $token): array
    {
        $key = file_get_contents(__DIR__ . '/../../config/keys/public.pem');

        /** @var array $data */
        $data = JWT::decode($token, $key, ['RS256']);

        return (array)$data;
    }
}
