<?php

namespace App\Console\Jobs;

use WebApp\Queue\JobInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class SendWelcomeJob implements JobInterface
{
    public function __construct(public readonly string $userId, public readonly string $email)
    {
    }

    public static function fromPayload(array $payload): static
    {
        return new static((string) ($payload['userId'] ?? ''), (string) ($payload['email'] ?? ''));
    }

    public function payload(): array
    {
        return ['userId' => $this->userId, 'email' => $this->email];
    }

    public function handle(ContainerInterface $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $logger->info('welcome.job', ['userId' => $this->userId, 'email' => $this->email]);
    }
}

