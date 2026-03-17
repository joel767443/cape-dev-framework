<?php

return [
    'default' => getenv('QUEUE_CONNECTION') ?: 'redis',

    // Namespace/prefix for Redis keys (not the queue name itself).
    'prefix' => getenv('QUEUE_PREFIX') ?: 'webapp_queue',

    // Default queue name.
    'queue' => getenv('QUEUE_NAME') ?: 'default',

    // Max attempts before moving to failed.
    'max_tries' => (int) (getenv('QUEUE_MAX_TRIES') ?: 3),

    // Seconds to wait before retrying a failed job.
    'retry_after' => (int) (getenv('QUEUE_RETRY_AFTER') ?: 5),

    'redis' => [
        // Reuse REDIS_DSN if present; allow override.
        'dsn' => getenv('QUEUE_REDIS_DSN') ?: (getenv('REDIS_DSN') ?: 'tcp://127.0.0.1:6379'),
    ],
];

