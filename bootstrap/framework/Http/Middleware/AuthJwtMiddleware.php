<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Http\Middleware;

use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Auth\Jwt\JwtService;
use WebApp\Http\Exception\HttpException;

/**
 *
 */
final class AuthJwtMiddleware implements MiddlewareInterface
{
    /**
     * @param JwtService $jwt
     */
    public function __construct(private readonly JwtService $jwt)
    {
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     * @throws JsonException
     */
    public function process(Request $request, callable $next): Response
    {
        $header = (string) $request->headers->get('Authorization', '');
        if ($header === '' || !str_starts_with($header, 'Bearer ')) {
            throw new HttpException(401, 'Missing bearer token');
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            throw new HttpException(401, 'Missing bearer token');
        }

        $claims = $this->jwt->verify($token);
        $request->attributes->set('auth', $claims);

        return $next($request);
    }
}

