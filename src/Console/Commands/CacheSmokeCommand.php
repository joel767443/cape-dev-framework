<?php

namespace WebApp\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CacheSmokeCommand extends Command
{
    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct('cache:smoke');
    }

    protected function configure(): void
    {
        $this->setDescription('Smoke test cache get/clear behavior.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = 'smoke_' . date('YmdHis');

        $first = $this->cache->get($key, function (ItemInterface $item): string {
            $item->expiresAfter(60);
            return 'v1-' . bin2hex(random_bytes(4));
        });
        $second = $this->cache->get($key, fn (): string => 'should-not-happen');

        $this->cache->delete($key);
        $third = $this->cache->get($key, function (ItemInterface $item): string {
            $item->expiresAfter(60);
            return 'v2-' . bin2hex(random_bytes(4));
        });

        $output->writeln("key={$key}");
        $output->writeln("first={$first}");
        $output->writeln("second={$second}");
        $output->writeln("third={$third}");

        if ($first !== $second && $third === $second) {
            $output->writeln('<error>Cache behavior unexpected.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>OK</info>');
        return Command::SUCCESS;
    }
}

