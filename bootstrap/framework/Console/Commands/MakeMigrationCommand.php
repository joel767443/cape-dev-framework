<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Console\Support\CodeWriter;
use WebApp\Console\Support\Naming;
use WebApp\Console\Support\Paths;

/**
 *
 */
final class MakeMigrationCommand extends Command
{
    /**
     * @param CodeWriter $writer
     */
    public function __construct(private readonly CodeWriter $writer)
    {
        parent::__construct('make:migration');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Create a new migration class in app/.')
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name (e.g. create_users_table)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite the file if it already exists');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $force = (bool) $input->getOption('force');

        $timestamp = date('Y_m_d_His');
        $class = Naming::studly($name);
        if ($class === '') {
            $output->writeln('<error>Invalid migration name.</error>');
            return Command::INVALID;
        }

        $namespace = 'App\\Database\\Migrations';
        $file = "{$timestamp}_{$class}.php";
        $path = Paths::databasePath($this->writer->rootPath(), 'migrations' . DIRECTORY_SEPARATOR . $file);

        $code = $this->render($namespace, $class);
        $this->writer->writeFile($path, $code, $force);

        $output->writeln("<info>Created</info> {$path}");
        return Command::SUCCESS;
    }

    private function render(string $namespace, string $class): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use App\\Database\\Migrations\\MigrationInterface;
use Illuminate\\Database\\ConnectionInterface;
use Illuminate\\Database\\Schema\\Builder;

final class {$class} implements MigrationInterface
{
    public function up(Builder \$schema, ConnectionInterface \$db): void
    {
        // TODO: Use \$schema->create() / \$schema->table() ...
    }

    public function down(Builder \$schema, ConnectionInterface \$db): void
    {
        // TODO: Reverse migration
    }
}

PHP;
    }
}

