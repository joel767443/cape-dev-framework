<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Queue\QueueInterface;

/**
 *
 */
final class QueueFailedClearCommand extends Command
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(private readonly QueueInterface $queue)
    {
        parent::__construct('queue:failed:clear');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Clear failed jobs.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->queue->clearFailed();
        $output->writeln("<info>Cleared</info> {$count} failed job(s).");
        return Command::SUCCESS;
    }
}

