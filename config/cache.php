<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    // Default store to use.
    'default' => getenv('CACHE_STORE') ?: 'redis',

    // Prefix/namespace for keys.
    'prefix' => getenv('CACHE_PREFIX') ?: 'webapp',

    // Default TTL (seconds) when not specified by callers.
    'ttl' => (int) (getenv('CACHE_TTL') ?: 0),

    'stores' => [
        'redis' => [
            'dsn' => getenv('REDIS_DSN') ?: '',
        ],
        'filesystem' => [
            'path' => getenv('CACHE_PATH') ?: 'bootstrap/cache',
        ],
    ],
];

