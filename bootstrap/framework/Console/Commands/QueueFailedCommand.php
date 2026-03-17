<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Queue\QueueInterface;

/**
 *
 */
final class QueueFailedCommand extends Command
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(private readonly QueueInterface $queue)
    {
        parent::__construct('queue:failed');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('List failed jobs.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Max failed jobs to show', 20);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = max(1, (int) $input->getOption('limit'));
        $items = $this->queue->failed($limit);
        if ($items === []) {
            $output->writeln('<info>No failed jobs.</info>');
            return Command::SUCCESS;
        }

        foreach ($items as $i => $item) {
            $job = (string) ($item['job'] ?? '');
            $queue = (string) ($item['queue'] ?? '');
            $failedAt = (string) ($item['failed_at'] ?? '');
            $error = (string) ($item['error'] ?? '');
            $output->writeln(sprintf('#%d %s [%s] %s', $i + 1, $job, $queue, $failedAt));
            if ($error !== '') {
                $output->writeln('  ' . $error);
            }
        }

        return Command::SUCCESS;
    }
}

