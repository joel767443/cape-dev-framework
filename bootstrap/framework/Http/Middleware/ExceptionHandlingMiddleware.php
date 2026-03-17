<?php

namespace WebApp\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use WebApp\Http\Exception\ExceptionHandler;

/**
 *
 */
final class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    /**
     * @param ExceptionHandler $handler
     */
    public function __construct(private readonly ExceptionHandler $handler)
    {
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function process(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            return $this->handler->render($request, $e);
        }
    }
}

