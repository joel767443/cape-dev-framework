<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Console;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application as SymfonyConsole;
use WebApp\Application;

/**
 *
 */
final class ConsoleApplicationFactory
{
    /**
     * @param string $rootPath
     * @return SymfonyConsole
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create(string $rootPath): SymfonyConsole
    {
        $app = new Application($rootPath);

        // Load routes the same way as the HTTP front controller.
        $router = $app->router;
        require $rootPath . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'api.php';

        $console = new SymfonyConsole('WebApp Console');
        (new ConsoleKernel($app, $console))->register();

        return $console;
    }
}

