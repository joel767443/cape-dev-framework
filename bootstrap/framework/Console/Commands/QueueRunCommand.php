<?php

namespace WebApp\Console\Commands;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use WebApp\Queue\QueueInterface;
use WebApp\Queue\RedisQueue;

/**
 *
 */
final class QueueRunCommand extends Command
{
    /**
     * @param QueueInterface $queue
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly QueueInterface $queue,
        private readonly ContainerInterface $container
    ) {
        parent::__construct('queue:run');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Run N queued jobs and exit (cron-friendly).')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Queue name')
            ->addOption('max', null, InputOption::VALUE_REQUIRED, 'Max jobs to process', 10)
            ->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'BRPOP timeout seconds', 1);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueName = $input->getOption('queue');
        $max = (int) $input->getOption('max');
        $timeout = (int) $input->getOption('timeout');
        $max = max(1, $max);
        $timeout = max(1, $timeout);

        $processed = 0;
        while ($processed < $max) {
            $job = $this->queue->pop(is_string($queueName) ? $queueName : null, $timeout);
            if (!$job) {
                break;
            }

            $processed++;
            $output->writeln("Processing {$job->jobClass} (attempt {$job->attempts})");

            try {
                if (!class_exists($job->jobClass)) {
                    throw new RuntimeException("Job class not found: {$job->jobClass}");
                }
                $instance = $job->jobClass::fromPayload($job->payload);
                $instance->handle($this->container);
            } catch (Throwable $e) {
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

        $output->writeln("<info>Done.</info> Processed {$processed} job(s).");
        return Command::SUCCESS;
    }
}

