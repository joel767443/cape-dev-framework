<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Config;

use Dotenv\Dotenv;

/**
 *
 */
final class Env
{
    /**
     * @param string $rootPath
     * @return void
     */
    public static function load(string $rootPath): void
    {
        $dotenvPath = rtrim($rootPath, DIRECTORY_SEPARATOR);
        if (!is_dir($dotenvPath)) {
            return;
        }

        // Loads .env if present; does nothing if missing.
        $dotenv = Dotenv::createImmutable($dotenvPath);
        $dotenv->safeLoad();
    }
}

