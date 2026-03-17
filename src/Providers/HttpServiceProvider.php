<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Http\Exception\ExceptionHandler;
use WebApp\Http\Middleware\CorsMiddleware;
use WebApp\Http\Middleware\ExceptionHandlingMiddleware;
use WebApp\Http\Middleware\ErrorLoggingMiddleware;
use WebApp\Http\Middleware\MiddlewareRegistry;
use WebApp\Http\Middleware\AuthJwtMiddleware;

final class HttpServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            MiddlewareRegistry::class => \DI\create(MiddlewareRegistry::class),

            ExceptionHandler::class => \DI\factory(function (): ExceptionHandler {
                return new ExceptionHandler((bool) config('app.debug', false));
            }),

            ExceptionHandlingMiddleware::class => \DI\autowire(ExceptionHandlingMiddleware::class),
            CorsMiddleware::class => \DI\autowire(CorsMiddleware::class),
            ErrorLoggingMiddleware::class => \DI\autowire(ErrorLoggingMiddleware::class),
            AuthJwtMiddleware::class => \DI\autowire(AuthJwtMiddleware::class),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
        /** @var MiddlewareRegistry $registry */
        $registry = $container->get(MiddlewareRegistry::class);
        $registry->alias('cors', CorsMiddleware::class);
        $registry->alias('exceptions', ExceptionHandlingMiddleware::class);
        $registry->alias('error_log', ErrorLoggingMiddleware::class);
        $registry->alias('auth_jwt', AuthJwtMiddleware::class);
    }
}

