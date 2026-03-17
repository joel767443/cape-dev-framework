<?php

namespace WebApp\Container;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

interface ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void;

    public function boot(ContainerInterface $container): void;
}

