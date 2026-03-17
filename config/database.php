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

    /**
     * Doctrine ORM (optional)
     *
     * Defaults to the same SQLite database file as Eloquent unless overridden.
     */
    'doctrine' => [
        // Where your Doctrine entity classes live.
        'entities_path' => getenv('DOCTRINE_ENTITIES_PATH') ?: 'app/Entities',

        // Toggle dev mode (disables metadata caching).
        'dev_mode' => in_array(strtolower((string) getenv('DOCTRINE_DEV_MODE')), ['1', 'true', 'yes', 'on'], true),

        // Connection parameters (DBAL).
        // For SQLite, set DOCTRINE_SQLITE_PATH (defaults to DB_SQLITE_PATH / project default).
        'connection' => [
            'driver' => getenv('DOCTRINE_DRIVER') ?: 'pdo_sqlite',
            'path' => getenv('DOCTRINE_SQLITE_PATH') ?: (getenv('DB_SQLITE_PATH') ?: null),
        ],
    ],
];

