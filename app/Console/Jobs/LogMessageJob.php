<?php

namespace App\Jobs;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use WebApp\Queue\JobInterface;

final class LogMessageJob implements JobInterface
{
    public function __construct(private readonly string $message)
    {
    }

    public function handle(ContainerInterface $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $logger->info('job.log_message', ['message' => $this->message]);
    }

    public function toPayload(): array
    {
        return ['message' => $this->message];
    }

    public static function fromPayload(array $payload): static
    {
        return new static((string) ($payload['message'] ?? ''));
    }
}

