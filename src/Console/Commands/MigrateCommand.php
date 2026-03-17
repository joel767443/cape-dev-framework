<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Console\Support\Paths;
use WebApp\Database\Database;
use WebApp\Database\Migrations\MigrationLoader;
use WebApp\Database\Migrations\MigrationRepository;
use WebApp\Database\Migrations\Migrator;

final class MigrateCommand extends Command
{
    public function __construct(private readonly string $rootPath)
    {
        parent::__construct('migrate');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run pending database migrations.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'List pending migrations without running them');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');

        $db = new Database();
        $repo = new MigrationRepository($db);
        $loader = new MigrationLoader(Paths::appPath($this->rootPath, 'Database/Migrations'));
        $available = $loader->discover();

        $migrator = new Migrator($db, $repo);
        $pending = $migrator->pending($available);

        if ($pending === []) {
            $output->writeln('<info>Nothing to migrate.</info>');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $output->writeln('<info>Pending migrations:</info>');
            foreach ($pending as $m) {
                $output->writeln(' - ' . $m['name']);
            }
            return Command::SUCCESS;
        }

        $output->writeln('<info>Running migrations...</info>');
        $ran = $migrator->run($pending);
        $output->writeln("<info>Done.</info> Ran {$ran} migration(s).");

        return Command::SUCCESS;
    }
}

