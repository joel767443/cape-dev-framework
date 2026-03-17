<?php

namespace WebApp\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Http\Exception\ExceptionHandler;

final class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ExceptionHandler $handler)
    {
    }

    public function process(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return $this->handler->render($request, $e);
        }
    }
}

