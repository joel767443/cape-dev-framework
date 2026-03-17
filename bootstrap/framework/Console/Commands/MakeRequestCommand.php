<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
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
final class MakeRequestCommand extends Command
{
    /**
     * @param CodeWriter $writer
     */
    public function __construct(private readonly CodeWriter $writer)
    {
        parent::__construct('make:request');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Create a new request class in app/ (FormRequest + Symfony Validator constraints).')
            ->addArgument('name', InputArgument::REQUIRED, 'Request class name (e.g. StoreUserRequest or Admin/StoreUserRequest)')
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

        $defaultNs = 'App\\Http\\Requests';
        $split = Naming::splitNamespaceAndClass($name, $defaultNs);
        $namespace = $split['namespace'] ?: $defaultNs;
        $class = Naming::studly($split['class']);

        if ($class === '') {
            $output->writeln('<error>Invalid request name.</error>');
            return Command::INVALID;
        }

        if (!str_ends_with($class, 'Request')) {
            $class .= 'Request';
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

use App\\Http\\Requests\\FormRequest;
use Symfony\\Component\\Validator\\Constraint;
use Symfony\\Component\\Validator\\Constraints as Assert;

final class {$class} extends FormRequest
{
    public function constraints(): Constraint|array
    {
        return new Assert\\Collection([
            // 'email' => [new Assert\\NotBlank(), new Assert\\Email()],
        ]);
    }
}

PHP;
    }
}

