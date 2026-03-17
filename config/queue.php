<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
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

    /**
     * Symfony Messenger (optional)
     *
     * Supported transports in this repo:
     * - sync (default): in-process message bus (no queue)
     * - in_memory: process-local queue useful for development/tests (requires a consumer process)
     */
    'messenger' => [
        'transport' => getenv('MESSENGER_TRANSPORT') ?: 'sync',
    ],
];

