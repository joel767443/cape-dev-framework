<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Console\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
final class DevCommand extends Command
{
    /**
     * @param string $rootPath
     */
    public function __construct(private readonly string $rootPath)
    {
        parent::__construct('dev');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Start backend dev server.')
            ->addOption('backend', null, InputOption::VALUE_REQUIRED, 'Backend host:port', 'localhost:8001');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backend = (string) $input->getOption('backend');
        $backendCmd = ['php', '-S', $backend, '-t', 'public', 'public/index.php'];

        $output->writeln("<info>Backend:</info> http://{$backend}");
        $backendProc = $this->spawn($backendCmd, $this->rootPath, $output);

        while (true) {
            $b = proc_get_status($backendProc);
            if (!$b['running']) {
                break;
            }
            usleep(200_000);
        }

        $this->terminate($backendProc);

        return Command::SUCCESS;
    }

    /**
     * @param string[] $cmd
     */
    private function spawn(array $cmd, string $cwd, OutputInterface $output)
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $proc = proc_open($cmd, $descriptors, $pipes, $cwd);
        if (!is_resource($proc)) {
            throw new RuntimeException('Failed to start process: ' . implode(' ', $cmd));
        }

        foreach ([1, 2] as $i) {
            stream_set_blocking($pipes[$i], false);
        }

        // Best-effort: stream child output in the background.
        register_shutdown_function(function () use ($proc): void {
            if (is_resource($proc)) {
                @proc_terminate($proc);
            }
        });

        return $proc;
    }

    /**
     * @param $proc
     * @return void
     */
    private function terminate($proc): void
    {
        if (is_resource($proc)) {
            @proc_terminate($proc);
            @proc_close($proc);
        }
    }
}

