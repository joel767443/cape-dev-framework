<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use WebApp\Cache\CacheFactory;
use WebApp\Container\ServiceProviderInterface;
use function DI\factory;

/**
 *
 */
final class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            CacheInterface::class => factory(function (): CacheInterface {
                return CacheFactory::create();
            }),
            CacheItemPoolInterface::class => factory(function (): CacheItemPoolInterface {
                return CacheFactory::create();
            }),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
    }
}

