<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace App\Console\Jobs;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WebApp\Queue\JobInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 */
final class SendWelcomeJob implements JobInterface
{
    /**
     * @param string $userId
     * @param string $email
     */
    public function __construct(public readonly string $userId, public readonly string $email)
    {
    }

    /**
     * @param array $payload
     * @return static
     */
    public static function fromPayload(array $payload): static
    {
        return new static((string) ($payload['userId'] ?? ''), (string) ($payload['email'] ?? ''));
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return ['userId' => $this->userId, 'email' => $this->email];
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
        $logger->info('welcome.job', ['userId' => $this->userId, 'email' => $this->email]);
    }
}

