<?php

namespace WebApp\Cache;

use Predis\Client as PredisClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use WebApp\Application;

/**
 *
 */
final class CacheFactory
{
    /**
     * @return AdapterInterface
     */
    public static function create(): AdapterInterface
    {
        $store = (string) config('cache.default', 'redis');
        $prefix = (string) config('cache.prefix', 'webapp');
        $ttl = (int) config('cache.ttl', 0);

        if ($store === 'redis') {
            $dsn = (string) config('cache.stores.redis.dsn', '');
            if ($dsn !== '') {
                $client = new PredisClient($dsn);
                return new RedisAdapter($client, $prefix, $ttl);
            }
        }

        $path = (string) config('cache.stores.filesystem.path', 'bootstrap/cache');
        if ($path !== '' && $path[0] !== DIRECTORY_SEPARATOR && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
            $path = rtrim(Application::$ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }

        return new FilesystemAdapter($prefix, $ttl, $path);
    }
}

