<?php

namespace WebApp\Console;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
use WebApp\Queue\QueueInterface;
use WebApp\Console\Commands\QueueRunCommand;
use WebApp\Console\Commands\QueueWorkCommand;
use WebApp\Console\Commands\QueueFailedCommand;
use WebApp\Console\Commands\QueueFailedClearCommand;
use WebApp\Console\Commands\QueueDispatchCommand;
use WebApp\Queue\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Doctrine\ORM\EntityManagerInterface;
use WebApp\Console\Commands\DoctrineSchemaUpdateCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use WebApp\Console\Commands\MessengerConsumeCommand;
use WebApp\Console\Commands\DevCommand;

/**
 *
 */
final class ConsoleKernel
{
    /**
     * @param Application $app
     * @param SymfonyConsole $console
     */
    public function __construct(
        private readonly Application $app,
        private readonly SymfonyConsole $console
    ) {
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(): void
    {
        $writer = new CodeWriter(Application::$ROOT_PATH);

        $this->console->addCommand(new RoutesListCommand($this->app->router->getRouteCollection()));

        $this->console->addCommand(new MakeControllerCommand($writer));
        $this->console->addCommand(new MakeModelCommand($writer));
        $this->console->addCommand(new MakeRequestCommand($writer));
        $this->console->addCommand(new MakeMigrationCommand($writer));

        $db = $this->app->container()->get(ConnectionInterface::class);
        $this->console->addCommand(new MigrateCommand(Application::$ROOT_PATH, $db));
        $this->console->addCommand(new SeedCommand());
        $cache = $this->app->container()->get(CacheInterface::class);
        $this->console->addCommand(new CacheClearCommand(Application::$ROOT_PATH, $cache));
        $this->console->addCommand(new CacheSmokeCommand($cache));

        $queue = $this->app->container()->get(QueueInterface::class);
        $this->console->addCommand(new QueueRunCommand($queue, $this->app->container()));
        $this->console->addCommand(new QueueWorkCommand($queue, $this->app->container()));
        $this->console->addCommand(new QueueFailedCommand($queue));
        $this->console->addCommand(new QueueFailedClearCommand($queue));
        $this->console->addCommand(new QueueDispatchCommand($this->app->container()->get(Dispatcher::class)));

        // Doctrine ORM (optional)
        $this->console->addCommand(new DoctrineSchemaUpdateCommand($this->app->container()->get(EntityManagerInterface::class)));

        // Symfony Messenger (optional)
        $this->console->addCommand(new MessengerConsumeCommand(
            $this->app->container()->get(ReceiverInterface::class),
            $this->app->container()->get(MessageBusInterface::class),
            $this->app->container()->get(EventDispatcherInterface::class),
        ));

        $this->console->addCommand(new DevCommand(Application::$ROOT_PATH));
    }
}

