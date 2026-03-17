<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Validation\RequestValidator;

final class ValidationServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            ValidatorInterface::class => \DI\factory(function (): ValidatorInterface {
                return Validation::createValidator();
            }),
            RequestValidator::class => \DI\autowire(RequestValidator::class),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

