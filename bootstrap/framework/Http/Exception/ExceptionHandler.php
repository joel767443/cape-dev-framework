<?php

namespace WebApp\Http\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 *
 */
final class ExceptionHandler
{
    /**
     * @param bool $debug
     */
    public function __construct(private readonly bool $debug = false)
    {
    }

    /**
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response
    {
        $wantsJson = $this->wantsJson($request);

        $status = 500;
        $code = 500;
        $message = 'Server Error';
        $details = [];

        if ($e instanceof HttpException) {
            $status = $e->statusCode;
            $code = $e->statusCode;
            $message = $e->getMessage() !== '' ? $e->getMessage() : $message;
            $details = $e->details;
        }

        if ($wantsJson) {
            $payload = [
                'success' => false,
                'status' => $status,
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ];

            if ($this->debug) {
                $payload['trace'] = explode("\n", $e->getTraceAsString());
            }

            return new JsonResponse($payload, $status);
        }

        $html = '<h1>' . htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') . '</h1>';
        if ($this->debug) {
            $html .= '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
        }

        return new Response($html, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function wantsJson(Request $request): bool
    {
        $path = $request->getPathInfo();
        if (str_starts_with($path, '/api')) {
            return true;
        }

        $accept = (string) $request->headers->get('Accept', '');
        return str_contains($accept, 'application/json') || str_contains($accept, '+json');
    }
}

