<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Queue;

/**
 *
 */
final class QueuedJob
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public readonly string $id,
        public readonly string $queue,
        public readonly string $jobClass,
        public readonly array $payload,
        public readonly int $attempts
    ) {
    }
}

