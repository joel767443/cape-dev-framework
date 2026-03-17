<?php

namespace WebApp\Http\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 *
 */
final class ErrorLoggingMiddleware implements MiddlewareInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     * @throws Throwable
     */
    public function process(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
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

