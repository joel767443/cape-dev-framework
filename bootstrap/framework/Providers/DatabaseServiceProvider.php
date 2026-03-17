<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;

final class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            Capsule::class => \DI\factory(function (): Capsule {
                $capsule = new Capsule();

                $root = (string) \WebApp\Application::$ROOT_PATH;
                $cfg = (array) config('database', []);
                $default = (string) ($cfg['default'] ?? 'sqlite');
                $connections = (array) ($cfg['connections'] ?? []);
                $conn = (array) ($connections[$default] ?? []);

                if (($conn['driver'] ?? '') === 'sqlite') {
                    $dbPath = (string) ($conn['database'] ?? '');
                    if ($dbPath === '') {
                        // Keep existing default location.
                        $dbPath = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'cape-dev.sqlite';
                    } elseif ($dbPath[0] !== DIRECTORY_SEPARATOR && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $dbPath)) {
                        $dbPath = $root . DIRECTORY_SEPARATOR . ltrim($dbPath, DIRECTORY_SEPARATOR);
                    }
                    $conn['database'] = $dbPath;
                }

                $capsule->addConnection($conn, $default);
                $capsule->setAsGlobal();
                $capsule->bootEloquent();

                return $capsule;
            }),

            ConnectionInterface::class => \DI\factory(function (ContainerInterface $c): ConnectionInterface {
                /** @var Capsule $capsule */
                $capsule = $c->get(Capsule::class);
                $default = (string) config('database.default', 'sqlite');
                return $capsule->getConnection($default);
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
        // Instantiate to ensure connection is bootstrapped early.
        $container->get(Capsule::class);
    }
}

