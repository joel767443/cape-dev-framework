<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use WebApp\Auth\Jwt\JwtService;
use WebApp\Container\ServiceProviderInterface;
use function DI\factory;

/**
 *
 */
final class AuthServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            JwtService::class => factory(function (): JwtService {
                return new JwtService(
                    (string) config('auth.jwt.secret', ''),
                    (string) config('auth.jwt.issuer', 'webapp'),
                    (int) config('auth.jwt.ttl', 3600)
                );
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

