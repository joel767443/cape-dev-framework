<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use WebApp\Auth\Jwt\JwtService;
use WebApp\Container\ServiceProviderInterface;

final class AuthServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            JwtService::class => \DI\factory(function (): JwtService {
                return new JwtService(
                    (string) config('auth.jwt.secret', ''),
                    (string) config('auth.jwt.issuer', 'webapp'),
                    (int) config('auth.jwt.ttl', 3600)
                );
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

