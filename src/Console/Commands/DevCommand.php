<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DevCommand extends Command
{
    public function __construct(private readonly string $rootPath)
    {
        parent::__construct('dev');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Start backend + frontend dev servers (Ctrl+C stops both).')
            ->addOption('backend', null, InputOption::VALUE_REQUIRED, 'Backend host:port', 'localhost:8001')
            ->addOption('frontend-port', null, InputOption::VALUE_REQUIRED, 'Frontend dev server port', 5174);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backend = (string) $input->getOption('backend');
        $frontendPort = (int) $input->getOption('frontend-port');
        $frontendPort = $frontendPort > 0 ? $frontendPort : 5174;

        $backendCmd = ['php', '-S', $backend, 'index.php'];
        $frontendCmd = ['npm', 'run', 'dev', '--', '--port', (string) $frontendPort];

        $output->writeln("<info>Backend:</info> http://{$backend}");
        $output->writeln("<info>Frontend:</info> http://localhost:{$frontendPort}");

        $backendProc = $this->spawn($backendCmd, $this->rootPath, $output);
        $frontendProc = $this->spawn($frontendCmd, $this->rootPath . DIRECTORY_SEPARATOR . 'front-end', $output);

        // Wait until one exits, then terminate both.
        while (true) {
            $b = proc_get_status($backendProc);
            $f = proc_get_status($frontendProc);

            if (!$b['running'] || !$f['running']) {
                break;
            }
            usleep(200_000);
        }

        $this->terminate($backendProc);
        $this->terminate($frontendProc);

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
            throw new \RuntimeException('Failed to start process: ' . implode(' ', $cmd));
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

    private function terminate($proc): void
    {
        if (is_resource($proc)) {
            @proc_terminate($proc);
            @proc_close($proc);
        }
    }
}

