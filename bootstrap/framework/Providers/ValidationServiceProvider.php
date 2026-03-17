<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Illuminate\Database\ConnectionInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Validation\RequestValidator;
use WebApp\Validation\Constraints\ExistsValidator;
use WebApp\Validation\Constraints\UniqueValidator;

final class ValidationServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            ValidatorInterface::class => \DI\factory(function (): ValidatorInterface {
                return Validation::createValidator();
            }),
            RequestValidator::class => \DI\autowire(RequestValidator::class),
            ExistsValidator::class => \DI\factory(function (ConnectionInterface $db): ExistsValidator {
                return new ExistsValidator($db);
            }),
            UniqueValidator::class => \DI\factory(function (ConnectionInterface $db): UniqueValidator {
                return new UniqueValidator($db);
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

