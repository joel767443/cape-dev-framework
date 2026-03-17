<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Http\Exception\ExceptionHandler;
use WebApp\Http\Middleware\CorsMiddleware;
use WebApp\Http\Middleware\ExceptionHandlingMiddleware;
use WebApp\Http\Middleware\ErrorLoggingMiddleware;
use WebApp\Http\Middleware\MiddlewareRegistry;
use WebApp\Http\Middleware\AuthJwtMiddleware;
use function DI\autowire;
use function DI\create;
use function DI\factory;

/**
 *
 */
final class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            MiddlewareRegistry::class => create(MiddlewareRegistry::class),

            ExceptionHandler::class => factory(function (): ExceptionHandler {
                return new ExceptionHandler((bool) config('app.debug', false));
            }),

            ExceptionHandlingMiddleware::class => autowire(ExceptionHandlingMiddleware::class),
            CorsMiddleware::class => autowire(CorsMiddleware::class),
            ErrorLoggingMiddleware::class => autowire(ErrorLoggingMiddleware::class),
            AuthJwtMiddleware::class => autowire(AuthJwtMiddleware::class),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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

