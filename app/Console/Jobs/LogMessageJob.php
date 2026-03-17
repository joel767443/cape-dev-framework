<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace App\Jobs;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use WebApp\Queue\JobInterface;

/**
 * class LogMessageJob
 */
final class LogMessageJob implements JobInterface
{
    /**
     * @param string $message
     */
    public function __construct(private readonly string $message)
    {
    }

    /**
     * @param ContainerInterface $container
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(ContainerInterface $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $logger->info('job.log_message', ['message' => $this->message]);
    }

    /**
     * @return string[]
     */
    public function toPayload(): array
    {
        return ['message' => $this->message];
    }

    /**
     * @param array $payload
     * @return static
     */
    public static function fromPayload(array $payload): static
    {
        return new static((string) ($payload['message'] ?? ''));
    }
}

