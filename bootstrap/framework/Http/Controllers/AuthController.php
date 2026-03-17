<?php

namespace WebApp\Http\Controllers;

use App\Events\UserRegistered;
use App\Models\User;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Requests\ValidateExampleRequest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Auth\Jwt\JwtService;
use WebApp\Http\Exception\HttpException;

/**
 *
 */
final class AuthController
{
    /**
     * @param Request $request
     * @param JwtService $jwt
     * @return Response
     */
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

    /**
     * @param Request $request
     * @return Response
     */
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

    /**
     * @param RegisterRequest $request
     * @param JwtService $jwt
     * @param EventDispatcherInterface $events
     * @return Response
     */
    public function register(RegisterRequest $request, JwtService $jwt, EventDispatcherInterface $events): Response
    {
        $data = $request->validated();

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new HttpException(422, 'Validation failed');
        }

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            throw new HttpException(422, 'Validation failed', [
                'errors' => ['email' => ['Email is already registered.']],
            ]);
        }

        $user = new User();
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->save();

        $events->dispatch(new UserRegistered($user));

        $token = $jwt->issue([
            'sub' => (string) $user->getKey(),
            'email' => (string) $user->email,
            'role' => 'user',
        ]);

        return new JsonResponse([
            'success' => true,
            'code' => 201,
            'message' => 'Registered',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => (string) $user->getKey(),
                    'email' => (string) $user->email,
                ],
            ],
        ], 201);
    }

    /**
     * @param LoginRequest $request
     * @param JwtService $jwt
     * @return Response
     */
    public function login(LoginRequest $request, JwtService $jwt): Response
    {
        $data = $request->validated();

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new HttpException(422, 'Validation failed');
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();
        if (!$user || !is_string($user->password) || !password_verify($password, $user->password)) {
            throw new HttpException(401, 'Invalid credentials');
        }

        $token = $jwt->issue([
            'sub' => (string) $user->getKey(),
            'email' => (string) $user->email,
            'role' => 'user',
        ]);

        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => (string) $user->getKey(),
                    'email' => (string) $user->email,
                ],
            ],
        ], 200);
    }

    /**
     * @param ValidateExampleRequest $request
     * @return Response
     */
    public function validateExample(ValidateExampleRequest $request): Response
    {
        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'Validated',
            'data' => $request->validated(),
        ], 200);
    }
}

