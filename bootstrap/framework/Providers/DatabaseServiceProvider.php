<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use WebApp\Application;
use WebApp\Container\ServiceProviderInterface;
use function DI\factory;

/**
 *
 */
final class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            Capsule::class => factory(function (): Capsule {
                $capsule = new Capsule();

                $root = Application::$ROOT_PATH;
                $cfg = (array) config('database', []);
                $default = (string) ($cfg['default'] ?? 'sqlite');
                $connections = (array) ($cfg['connections'] ?? []);
                $conn = (array) ($connections[$default] ?? []);

                if (($conn['driver'] ?? '') === 'sqlite') {
                    $dbPath = (string) ($conn['database'] ?? '');
                    if ($dbPath === '') {
                        // Keep existing default location.
                        $dbPath = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'cape-dev.sqlite';
                    } elseif ($dbPath[0] !== DIRECTORY_SEPARATOR && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $dbPath)) {
                        $dbPath = $root . DIRECTORY_SEPARATOR . ltrim($dbPath, DIRECTORY_SEPARATOR);
                    }

                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    if (!is_file($dbPath)) {
                        @touch($dbPath);
                    }
                    $conn['database'] = $dbPath;
                }

                $capsule->addConnection($conn, $default);
                $capsule->setAsGlobal();
                $capsule->bootEloquent();

                return $capsule;
            }),

            ConnectionInterface::class => factory(function (ContainerInterface $c): ConnectionInterface {
                /** @var Capsule $capsule */
                $capsule = $c->get(Capsule::class);
                $default = (string) config('database.default', 'sqlite');
                return $capsule->getConnection($default);
            }),
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
        // Instantiate to ensure connection is bootstrapped early.
        $container->get(Capsule::class);
    }
}

