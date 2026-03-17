<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use WebApp\Application;
use WebApp\Container\ServiceProviderInterface;
use function DI\factory;

/**
 *
 */
final class DoctrineServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            EntityManagerInterface::class => factory(function (): EntityManagerInterface {
                $root = Application::$ROOT_PATH;
                $cfg = (array) config('database.doctrine', []);

                $entitiesPath = (string) ($cfg['entities_path'] ?? 'app/Entities');
                $devMode = (bool) ($cfg['dev_mode'] ?? false);
                $conn = (array) ($cfg['connection'] ?? []);

                $entitiesDir = $this->resolvePath($root, $entitiesPath);
                $config = ORMSetup::createAttributeMetadataConfiguration([$entitiesDir], $devMode);

                $connDriver = (string) ($conn['driver'] ?? 'pdo_sqlite');
                if ($connDriver === 'pdo_sqlite') {
                    $path = (string) ($conn['path'] ?? '');
                    if ($path === '') {
                        $path = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'cape-dev.sqlite';
                    } else {
                        $path = $this->resolvePath($root, $path);
                    }
                    $conn['path'] = $path;
                }

                $connection = DriverManager::getConnection($conn);
                return new EntityManager($connection, $config);
            }),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
        // No-op: Doctrine is opt-in; resolved on demand.
    }

    private function resolvePath(string $root, string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return $root;
        }
        if ($path[0] === DIRECTORY_SEPARATOR || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
            return $path;
        }
        return $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}

