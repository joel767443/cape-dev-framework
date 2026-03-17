<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use WebApp\Container\ServiceProviderInterface;

final class LoggingServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            LoggerInterface::class => \DI\factory(function (): LoggerInterface {
                $path = (string) config('logging.path', 'storage/logs/app.log');
                $levelName = (string) config('logging.level', 'info');

                $logger = new Logger('app');
                $logger->pushHandler(new StreamHandler($path, Logger::toMonologLevel($levelName)));
                return $logger;
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

