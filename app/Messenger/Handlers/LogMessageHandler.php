<?php

namespace App\Messenger\Handlers;

use App\Messenger\Messages\LogMessage;
use Psr\Log\LoggerInterface;

final class LogMessageHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(LogMessage $message): void
    {
        $this->logger->info('[messenger] ' . $message->message);
    }
}

