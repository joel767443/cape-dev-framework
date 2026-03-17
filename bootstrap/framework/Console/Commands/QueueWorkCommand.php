<?php

namespace WebApp\Console\Commands;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebApp\Queue\QueueInterface;
use WebApp\Queue\RedisQueue;

final class QueueWorkCommand extends Command
{
    public function __construct(
        private readonly QueueInterface $queue,
        private readonly ContainerInterface $container
    ) {
        parent::__construct('queue:work');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run a long-running queue worker (daemon).')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Queue name', null)
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep seconds when idle', 1)
            ->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'BRPOP timeout seconds', 5);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueName = $input->getOption('queue');
        $sleep = max(0, (int) $input->getOption('sleep'));
        $timeout = max(1, (int) $input->getOption('timeout'));

        $output->writeln('<info>Queue worker started.</info> Press Ctrl+C to stop.');

        while (true) {
            $job = $this->queue->pop(is_string($queueName) ? $queueName : null, $timeout);
            if (!$job) {
                if ($sleep > 0) {
                    sleep($sleep);
                }
                continue;
            }

            $output->writeln("Processing {$job->jobClass} (attempt {$job->attempts})");

            try {
                if (!class_exists($job->jobClass)) {
                    throw new \RuntimeException("Job class not found: {$job->jobClass}");
                }
                $instance = $job->jobClass::fromPayload($job->payload);
                $instance->handle($this->container);
            } catch (\Throwable $e) {
                $maxTries = (int) config('queue.max_tries', 3);
                $retryAfter = (int) config('queue.retry_after', 5);

                $attempts = $job->attempts + 1;
                if ($attempts < $maxTries && $this->queue instanceof RedisQueue) {
                    $output->writeln("<comment>Retrying in {$retryAfter}s:</comment> {$e->getMessage()}");
                    $this->queue->release($job->queue, $job->jobClass, $job->payload, $attempts, $retryAfter);
                } elseif ($this->queue instanceof RedisQueue) {
                    $output->writeln("<error>Failed:</error> {$e->getMessage()}");
                    $this->queue->fail($job->queue, $job->jobClass, $job->payload, $attempts, $e);
                } else {
                    throw $e;
                }
            }
        }

        // Unreachable
        // return Command::SUCCESS;
    }
}

