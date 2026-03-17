<?php

namespace WebApp\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Auth\Jwt\JwtService;
use WebApp\Http\Exception\HttpException;

final class AuthController
{
    public function token(Request $request, JwtService $jwt): Response
    {
        $raw = (string) $request->getContent();
        $payload = [];
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $subject = (string) ($payload['sub'] ?? 'demo');
        if ($subject === '') {
            throw new HttpException(422, 'Validation failed', ['errors' => ['sub' => ['sub is required']]]);
        }

        $token = $jwt->issue([
            'sub' => $subject,
            'role' => (string) ($payload['role'] ?? 'user'),
        ]);

        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => ['token' => $token],
        ], 200);
    }

    public function ping(Request $request): Response
    {
        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [
                'auth' => $request->attributes->get('auth'),
            ],
        ], 200);
    }
}

