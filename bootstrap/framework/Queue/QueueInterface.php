<?php

namespace WebApp\Queue;

interface QueueInterface
{
    public function push(JobInterface $job, ?string $queue = null): void;

    /**
     * Blocking pop.
     */
    public function pop(?string $queue = null, int $timeoutSeconds = 5): ?QueuedJob;

    public function later(int $delaySeconds, JobInterface $job, ?string $queue = null): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function failed(int $limit = 50): array;

    public function clearFailed(): int;
}

