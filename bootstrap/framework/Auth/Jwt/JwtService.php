<?php

namespace WebApp\Auth\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use WebApp\Http\Exception\HttpException;

final class JwtService
{
    public function __construct(
        private readonly string $secret,
        private readonly string $issuer,
        private readonly int $ttlSeconds
    ) {
        if ($this->secret === '') {
            throw new \RuntimeException('JWT secret is not configured. Set JWT_SECRET or APP_KEY.');
        }
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function issue(array $claims): string
    {
        $now = time();
        $payload = array_merge($claims, [
            'iss' => $this->issuer,
            'iat' => $now,
            'exp' => $now + $this->ttlSeconds,
        ]);

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $e) {
            throw new HttpException(401, 'Invalid token');
        }

        return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }
}

