<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Container;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 *
 */
final class ContainerFactory
{
    /**
     * @param ServiceProviderInterface[] $providers
     * @return ContainerInterface
     * @throws \Exception
     */
    public static function build(array $providers = []): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAttributes(false);

        foreach ($providers as $provider) {
            $provider->register($builder);
        }

        $container = $builder->build();

        foreach ($providers as $provider) {
            $provider->boot($container);
        }

        return $container;
    }
}

