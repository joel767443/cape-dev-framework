<?php

namespace WebApp\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @param string $allowOrigin
     * @param string $allowMethods
     * @param string $allowHeaders
     */
    public function __construct(
        private readonly string $allowOrigin = '*',
        private readonly string $allowMethods = 'GET, POST, OPTIONS, DELETE',
        private readonly string $allowHeaders = 'Content-Type'
    ) {
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function process(Request $request, callable $next): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response('', 204);
        } else {
            $response = $next($request);
        }

        $response->headers->set('Access-Control-Allow-Origin', $this->allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', $this->allowMethods);
        $response->headers->set('Access-Control-Allow-Headers', $this->allowHeaders);

        return $response;
    }
}

