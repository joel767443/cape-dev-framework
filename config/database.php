<?php

return [
    // Legacy key (kept for WebApp\Database\Database and compatibility)
    'sqlitePath' => getenv('DB_SQLITE_PATH') ?: null,

    // Illuminate/Eloquent configuration
    'default' => getenv('DB_CONNECTION') ?: 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => getenv('DB_SQLITE_PATH') ?: null,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],
];

