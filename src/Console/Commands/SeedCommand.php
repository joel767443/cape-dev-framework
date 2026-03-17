<?php

namespace WebApp\Console\Commands;

use App\Database\Seeders\SeederInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Database\Database;

final class SeedCommand extends Command
{
    public function __construct()
    {
        parent::__construct('seed');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seed the database.')
            ->addOption('class', null, InputOption::VALUE_REQUIRED, 'Seeder class to run', 'App\\Database\\Seeders\\DatabaseSeeder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = (string) $input->getOption('class');
        if ($class === '') {
            $output->writeln('<error>Seeder class is required.</error>');
            return Command::INVALID;
        }

        if (!class_exists($class)) {
            $output->writeln("<error>Seeder class not found:</error> {$class}");
            return Command::FAILURE;
        }

        $seeder = new $class();
        if (!$seeder instanceof SeederInterface) {
            $output->writeln("<error>Seeder must implement SeederInterface:</error> {$class}");
            return Command::FAILURE;
        }

        $db = new Database();
        $seeder->run($db);

        $output->writeln("<info>Seeded:</info> {$class}");
        return Command::SUCCESS;
    }
}

