<?php

namespace App\Messenger\Messages;

/**
 *
 */
final class LogMessage
{
    /**
     * @param string $message
     */
    public function __construct(public readonly string $message)
    {
    }
}

