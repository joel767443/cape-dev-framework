<?php

namespace WebApp\Container;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 *
 */
interface ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void;

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void;
}

