<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Queue\Dispatcher;
use WebApp\Queue\JobInterface;

/**
 *
 */
final class QueueDispatchCommand extends Command
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(private readonly Dispatcher $dispatcher)
    {
        parent::__construct('queue:dispatch');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Dispatch a job by class name (dev helper).')
            ->addArgument('job', InputArgument::REQUIRED, 'Job FQCN (must implement JobInterface)')
            ->addOption('payload', null, InputOption::VALUE_REQUIRED, 'JSON payload for Job::fromPayload()', '{}')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Queue name')
            ->addOption('delay', null, InputOption::VALUE_REQUIRED, 'Delay seconds', 0);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobClass = (string) $input->getArgument('job');
        $payloadJson = (string) $input->getOption('payload');
        $queue = $input->getOption('queue');
        $delay = (int) $input->getOption('delay');

        if (!class_exists($jobClass)) {
            $output->writeln("<error>Job class not found:</error> {$jobClass}");
            return Command::FAILURE;
        }
        if (!is_subclass_of($jobClass, JobInterface::class)) {
            $output->writeln("<error>Job must implement JobInterface:</error> {$jobClass}");
            return Command::FAILURE;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            $output->writeln('<error>Invalid JSON payload.</error>');
            return Command::INVALID;
        }

        /** @var class-string<JobInterface> $jobClass */
        $job = $jobClass::fromPayload($payload);

        if ($delay > 0) {
            $this->dispatcher->dispatchLater($delay, $job, is_string($queue) ? $queue : null);
        } else {
            $this->dispatcher->dispatch($job, is_string($queue) ? $queue : null);
        }

        $output->writeln("<info>Dispatched</info> {$jobClass}");
        return Command::SUCCESS;
    }
}

