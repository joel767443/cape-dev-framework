<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Worker;

final class MessengerConsumeCommand extends Command
{
    public function __construct(
        private readonly ReceiverInterface $receiver,
        private readonly MessageBusInterface $bus,
        private readonly EventDispatcherInterface $events
    ) {
        parent::__construct('messenger:consume');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Consume messages from configured Messenger transport.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Max messages to handle', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = max(1, (int) $input->getOption('limit'));

        $worker = new Worker([$this->receiver], $this->bus, $this->events);

        // Stop after N messages. This keeps it simple and CLI-friendly.
        $handled = 0;
        $this->events->addListener(\Symfony\Component\Messenger\Event\WorkerMessageHandledEvent::class, function () use (&$handled, $limit, $worker): void {
            $handled++;
            if ($handled >= $limit) {
                $worker->stop();
            }
        });

        $output->writeln("<info>Consuming messages (limit={$limit})...</info>");
        $worker->run();
        $output->writeln('<info>Done.</info>');

        return Command::SUCCESS;
    }
}

