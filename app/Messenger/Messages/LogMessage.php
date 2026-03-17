<?php

namespace App\Messenger\Messages;

final class LogMessage
{
    public function __construct(public readonly string $message)
    {
    }
}

