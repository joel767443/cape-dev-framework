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

final class MakeControllerCommand extends Command
{
    public function __construct(private readonly CodeWriter $writer)
    {
        parent::__construct('make:controller');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new controller class in app/.')
            ->addArgument('name', InputArgument::REQUIRED, 'Controller class name (e.g. UserController or Admin/UserController)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite the file if it already exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $force = (bool) $input->getOption('force');

        $defaultNs = 'App\\Http\\Controllers';
        $split = Naming::splitNamespaceAndClass($name, $defaultNs);
        $namespace = $split['namespace'] ?: $defaultNs;
        $class = Naming::studly($split['class']);

        if ($class === '') {
            $output->writeln('<error>Invalid controller name.</error>');
            return Command::INVALID;
        }

        if (!str_ends_with($class, 'Controller')) {
            $class .= 'Controller';
        }

        $relativeNs = str_starts_with($namespace, 'App\\') ? substr($namespace, 4) : $namespace;
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, trim($relativeNs, '\\'));
        $path = Paths::appPath($this->writer->rootPath(), $relativePath . DIRECTORY_SEPARATOR . $class . '.php');

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

use Symfony\\Component\\HttpFoundation\\JsonResponse;
use Symfony\\Component\\HttpFoundation\\Request;
use Symfony\\Component\\HttpFoundation\\Response;

final class {$class}
{
    public function __invoke(Request \$request): Response
    {
        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [],
        ], 200);
    }
}

PHP;
    }
}

