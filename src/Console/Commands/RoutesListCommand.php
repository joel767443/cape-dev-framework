<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouteCollection;

final class RoutesListCommand extends Command
{
    public function __construct(private readonly RouteCollection $routes)
    {
        parent::__construct('route:list');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List registered routes.')
            ->setAliases(['routes:list']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->routes->all() as $name => $route) {
            $methods = $route->getMethods();
            $methodStr = $methods ? implode('|', $methods) : 'ANY';
            $output->writeln(sprintf('%s  %s  %s', str_pad($methodStr, 12), str_pad($route->getPath(), 40), $name));
        }

        return Command::SUCCESS;
    }
}

