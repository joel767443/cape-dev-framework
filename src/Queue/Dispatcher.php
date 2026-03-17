<?php

namespace WebApp\Queue;

final class Dispatcher
{
    public function __construct(private readonly QueueInterface $queue)
    {
    }

    public function dispatch(JobInterface $job, ?string $queue = null): void
    {
        $this->queue->push($job, $queue);
    }

    public function dispatchLater(int $delaySeconds, JobInterface $job, ?string $queue = null): void
    {
        $this->queue->later($delaySeconds, $job, $queue);
    }
}

