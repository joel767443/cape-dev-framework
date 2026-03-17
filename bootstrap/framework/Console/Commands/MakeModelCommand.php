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

final class MakeModelCommand extends Command
{
    public function __construct(private readonly CodeWriter $writer)
    {
        parent::__construct('make:model');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new model class in app/.')
            ->addArgument('name', InputArgument::REQUIRED, 'Model class name (e.g. User or Billing/Invoice)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite the file if it already exists')
            ->addOption('table', 't', InputOption::VALUE_REQUIRED, 'Explicit table name (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $force = (bool) $input->getOption('force');
        $table = (string) ($input->getOption('table') ?? '');

        $defaultNs = 'App\\Models';
        $split = Naming::splitNamespaceAndClass($name, $defaultNs);
        $namespace = $split['namespace'] ?: $defaultNs;
        $class = Naming::studly($split['class']);

        if ($class === '') {
            $output->writeln('<error>Invalid model name.</error>');
            return Command::INVALID;
        }

        $relativeNs = str_starts_with($namespace, 'App\\') ? substr($namespace, 4) : $namespace;
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, trim($relativeNs, '\\'));
        $path = Paths::appPath($this->writer->rootPath(), $relativePath . DIRECTORY_SEPARATOR . $class . '.php');

        $code = $this->render($namespace, $class, $table);
        $this->writer->writeFile($path, $code, $force);

        $output->writeln("<info>Created</info> {$path}");
        return Command::SUCCESS;
    }

    private function render(string $namespace, string $class, string $table): string
    {
        $tableLine = $table !== '' ? "    protected static string \$table = '{$table}';\n\n" : '';

        return <<<PHP
<?php

namespace {$namespace};

final class {$class}
{
{$tableLine}    public function rules(): array
    {
        return [];
    }
}

PHP;
    }
}

