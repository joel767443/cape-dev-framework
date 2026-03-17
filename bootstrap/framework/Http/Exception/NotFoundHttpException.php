<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Http\Exception;

use Throwable;

/**
 *
 */
final class NotFoundHttpException extends HttpException
{
    /**
     * @param string $message
     * @param array $details
     * @param Throwable|null $previous
     */
    public function __construct(string $message = 'Not Found', array $details = [], ?Throwable $previous = null)
    {
        parent::__construct(404, $message, $details, $previous);
    }
}

