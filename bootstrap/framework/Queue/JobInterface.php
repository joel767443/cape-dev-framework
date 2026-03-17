<?php

namespace WebApp\Queue;

use Psr\Container\ContainerInterface;

interface JobInterface
{
    /**
     * Execute the job.
     */
    public function handle(ContainerInterface $container): void;

    /**
     * Return a JSON-serializable payload to re-create this job.
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array;

    /**
     * Re-create a job instance from payload.
     *
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload): static;
}

