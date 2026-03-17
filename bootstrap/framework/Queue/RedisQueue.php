<?php

namespace WebApp\Queue;

use Predis\Client as PredisClient;
use Throwable;

/**
 *
 */
final class RedisQueue implements QueueInterface
{
    /**
     * @param PredisClient $redis
     * @param string $prefix
     * @param string $defaultQueue
     */
    public function __construct(
        private readonly PredisClient $redis,
        private readonly string $prefix,
        private readonly string $defaultQueue
    ) {
    }

    /**
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function push(JobInterface $job, ?string $queue = null): void
    {
        $queue = $queue ?: $this->defaultQueue;
        $payload = $this->encodePayload($job, $queue, attempts: 0, availableAt: time());
        $this->redis->lpush($this->queueKey($queue), [$payload]);
    }

    /**
     * @param int $delaySeconds
     * @param JobInterface $job
     * @param string|null $queue
     * @return void
     */
    public function later(int $delaySeconds, JobInterface $job, ?string $queue = null): void
    {
        $queue = $queue ?: $this->defaultQueue;
        $availableAt = time() + max(0, $delaySeconds);
        $payload = $this->encodePayload($job, $queue, attempts: 0, availableAt: $availableAt);
        $this->redis->zadd($this->delayedKey($queue), [$payload => $availableAt]);
    }

    /**
     * @param string|null $queue
     * @param int $timeoutSeconds
     * @return QueuedJob|null
     */
    public function pop(?string $queue = null, int $timeoutSeconds = 5): ?QueuedJob
    {
        $queue = $queue ?: $this->defaultQueue;

        $this->migrateDue($queue);

        $key = $this->queueKey($queue);
        /** @var array{0:string,1:string}|null $result */
        $result = $this->redis->brpop([$key], max(1, $timeoutSeconds));
        if (!$result) {
            return null;
        }

        $payloadStr = $result[1] ?? '';
        $decoded = json_decode($payloadStr, true);
        if (!is_array($decoded)) {
            // Bad payload; move to failed.
            $this->pushFailed($queue, [
                'error' => 'Invalid payload JSON',
                'raw' => $payloadStr,
                'failed_at' => date('c'),
            ]);
            return null;
        }

        $jobClass = (string) ($decoded['job'] ?? '');
        $id = (string) ($decoded['id'] ?? '');
        $attempts = (int) ($decoded['attempts'] ?? 0);
        $payload = (array) ($decoded['payload'] ?? []);

        if ($jobClass === '' || $id === '') {
            $this->pushFailed($queue, [
                'error' => 'Missing job/id in payload',
                'decoded' => $decoded,
                'failed_at' => date('c'),
            ]);
            return null;
        }

        return new QueuedJob($id, $queue, $jobClass, $payload, $attempts);
    }

    /**
     * Requeue a job with incremented attempts and delay.
     *
     * @param array<string, mixed> $payload
     */
    public function release(string $queue, string $jobClass, array $payload, int $attempts, int $delaySeconds): void
    {
        $availableAt = time() + max(0, $delaySeconds);
        $job = $jobClass::fromPayload($payload);
        $payloadStr = $this->encodePayload($job, $queue, $attempts, $availableAt);
        $this->redis->zadd($this->delayedKey($queue), [$payloadStr => $availableAt]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function fail(string $queue, string $jobClass, array $payload, int $attempts, Throwable $e): void
    {
        $this->pushFailed($queue, [
            'job' => $jobClass,
            'payload' => $payload,
            'attempts' => $attempts,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'failed_at' => date('c'),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function failed(int $limit = 50): array
    {
        $key = $this->failedKey();
        $items = $this->redis->lrange($key, 0, max(0, $limit - 1));
        $out = [];
        foreach ($items as $raw) {
            $decoded = json_decode($raw, true);
            $out[] = is_array($decoded) ? $decoded : ['raw' => $raw];
        }
        return $out;
    }

    /**
     * @return int
     */
    public function clearFailed(): int
    {
        $key = $this->failedKey();
        $count = $this->redis->llen($key);
        $this->redis->del([$key]);
        return $count;
    }

    private function migrateDue(string $queue): void
    {
        $delayedKey = $this->delayedKey($queue);
        $now = time();

        // Pull a small batch of due jobs each time; worker loops will drain gradually.
        $due = $this->redis->zrangebyscore($delayedKey, '-inf', (string) $now, ['limit' => [0, 100]]);
        if (!$due) {
            return;
        }

        foreach ($due as $payloadStr) {
            $this->redis->zrem($delayedKey, [$payloadStr]);
            $this->redis->lpush($this->queueKey($queue), [$payloadStr]);
        }
    }

    private function queueKey(string $queue): string
    {
        return "{$this->prefix}:queue:{$queue}";
    }

    private function delayedKey(string $queue): string
    {
        return "{$this->prefix}:delayed:{$queue}";
    }

    private function failedKey(): string
    {
        return "{$this->prefix}:failed";
    }

    /**
     * @param array<string, mixed> $item
     */
    private function pushFailed(string $queue, array $item): void
    {
        $item['queue'] = $queue;
        $this->redis->lpush($this->failedKey(), [json_encode($item, JSON_UNESCAPED_SLASHES)]);
    }

    private function encodePayload(JobInterface $job, string $queue, int $attempts, int $availableAt): string
    {
        $id = bin2hex(random_bytes(16));

        return json_encode([
            'id' => $id,
            'queue' => $queue,
            'job' => $job::class,
            'payload' => $job->toPayload(),
            'attempts' => $attempts,
            'available_at' => $availableAt,
        ], JSON_UNESCAPED_SLASHES);
    }
}

