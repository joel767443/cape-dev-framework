<?php

namespace WebApp\Queue;

/**
 *
 */
final class Dispatcher
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(private readonly QueueInterface $queue)
    {
    }

    /**
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function dispatch(JobInterface $job, ?string $queue = null): void
    {
        $this->queue->push($job, $queue);
    }

    /**
     * @param int $delaySeconds
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function dispatchLater(int $delaySeconds, JobInterface $job, ?string $queue = null): void
    {
        $this->queue->later($delaySeconds, $job, $queue);
    }
}

