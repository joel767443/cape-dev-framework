<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use WebApp\Cache\CacheFactory;
use WebApp\Container\ServiceProviderInterface;

final class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            CacheInterface::class => \DI\factory(function (): CacheInterface {
                return CacheFactory::create();
            }),
            CacheItemPoolInterface::class => \DI\factory(function (): CacheItemPoolInterface {
                return CacheFactory::create();
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

