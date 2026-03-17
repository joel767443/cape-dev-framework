<?php

namespace WebApp\Console\Support;

/**
 *
 */
final class Paths
{
    /**
     * @param string $rootPath
     * @param string $relative
     * @return string
     */
    public static function appPath(string $rootPath, string $relative = ''): string
    {
        $base = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'app';
        $relative = ltrim($relative, DIRECTORY_SEPARATOR);
        return $relative === '' ? $base : ($base . DIRECTORY_SEPARATOR . $relative);
    }

    /**
     * @param string $rootPath
     * @param string $relative
     * @return string
     */
    public static function databasePath(string $rootPath, string $relative = ''): string
    {
        $base = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'database';
        $relative = ltrim($relative, DIRECTORY_SEPARATOR);
        return $relative === '' ? $base : ($base . DIRECTORY_SEPARATOR . $relative);
    }
}

