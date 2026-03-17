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
use function DI\autowire;
use function DI\factory;

/**
 *
 */
final class ValidationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            ValidatorInterface::class => factory(function (): ValidatorInterface {
                return Validation::createValidator();
            }),
            RequestValidator::class => autowire(RequestValidator::class),
            ExistsValidator::class => factory(function (ConnectionInterface $db): ExistsValidator {
                return new ExistsValidator($db);
            }),
            UniqueValidator::class => factory(function (ConnectionInterface $db): UniqueValidator {
                return new UniqueValidator($db);
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

