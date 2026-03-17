<?php

namespace WebApp\Console;

use Symfony\Component\Console\Application as SymfonyConsole;
use WebApp\Application;

final class ConsoleApplicationFactory
{
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

