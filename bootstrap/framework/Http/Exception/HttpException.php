<?php

namespace WebApp\Http\Exception;

use RuntimeException;

class HttpException extends RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        string $message = '',
        public readonly array $details = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}

