<?php

namespace WebApp\Http\Exception;

use RuntimeException;
use Throwable;

/**
 *
 */
class HttpException extends RuntimeException
{
    /**
     * @param int $statusCode
     * @param string $message
     * @param array $details
     * @param Throwable|null $previous
     */
    public function __construct(
        public readonly int $statusCode,
        string $message = '',
        public readonly array $details = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}

