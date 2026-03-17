<?php

namespace WebApp\Http\Exception;

final class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = 'Not Found', array $details = [], ?\Throwable $previous = null)
    {
        parent::__construct(404, $message, $details, $previous);
    }
}

