<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace App\Messenger\Handlers;

use App\Messenger\Messages\LogMessage;
use Psr\Log\LoggerInterface;

/**
 *
 */
final class LogMessageHandler
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param LogMessage $message
     * @return void
     */
    public function __invoke(LogMessage $message): void
    {
        $this->logger->info('[messenger] ' . $message->message);
    }
}

