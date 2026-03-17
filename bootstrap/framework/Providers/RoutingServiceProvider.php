<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;

/**
 *
 */
final class RoutingServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        // No container bindings needed yet; routing is configured by loading route files.
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
        // No-op. Routes are loaded from index.php for now (next: move to app bootstrap).
    }
}

