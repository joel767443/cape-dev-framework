<?php

namespace WebApp\Console;

use Symfony\Component\Console\Application as SymfonyConsole;
use WebApp\Application;
use WebApp\Console\Commands\MakeControllerCommand;
use WebApp\Console\Commands\MakeMigrationCommand;
use WebApp\Console\Commands\MakeModelCommand;
use WebApp\Console\Commands\MakeRequestCommand;
use WebApp\Console\Commands\MigrateCommand;
use WebApp\Console\Commands\RoutesListCommand;
use WebApp\Console\Commands\SeedCommand;
use WebApp\Console\Commands\CacheClearCommand;
use WebApp\Console\Commands\CacheSmokeCommand;
use WebApp\Console\Support\CodeWriter;
use Symfony\Contracts\Cache\CacheInterface;

final class ConsoleKernel
{
    public function __construct(
        private readonly Application $app,
        private readonly SymfonyConsole $console
    ) {
    }

    public function register(): void
    {
        $writer = new CodeWriter(Application::$ROOT_PATH);

        $this->console->addCommand(new RoutesListCommand($this->app->router->getRouteCollection()));

        $this->console->addCommand(new MakeControllerCommand($writer));
        $this->console->addCommand(new MakeModelCommand($writer));
        $this->console->addCommand(new MakeRequestCommand($writer));
        $this->console->addCommand(new MakeMigrationCommand($writer));

        $this->console->addCommand(new MigrateCommand(Application::$ROOT_PATH));
        $this->console->addCommand(new SeedCommand());
        $cache = $this->app->container()->get(CacheInterface::class);
        $this->console->addCommand(new CacheClearCommand(Application::$ROOT_PATH, $cache));
        $this->console->addCommand(new CacheSmokeCommand($cache));
    }
}

