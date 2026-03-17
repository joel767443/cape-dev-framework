<?php

namespace WebApp\Http\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ErrorLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function process(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            $this->logger->error('Unhandled exception', [
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);
            throw $e;
        }
    }
}

