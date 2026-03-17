<?php

namespace WebApp\Queue;

/**
 *
 */
interface QueueInterface
{
    /**
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function push(JobInterface $job, ?string $queue = null): void;

    /**
     * Blocking pop.
     */
    public function pop(?string $queue = null, int $timeoutSeconds = 5): ?QueuedJob;

    /**
     * @param int $delaySeconds
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function later(int $delaySeconds, JobInterface $job, ?string $queue = null): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function failed(int $limit = 50): array;

    /**
     * @return int
     */
    public function clearFailed(): int;
}

