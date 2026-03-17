<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Console\Commands;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
final class CacheClearCommand extends Command
{
    /**
     * @param string $rootPath
     * @param CacheInterface $cache
     */
    public function __construct(
        private readonly string $rootPath,
        private readonly CacheInterface $cache
    )
    {
        parent::__construct('cache:clear');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Clear application cache files.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Clear the active cache pool (Redis/filesystem, depending on config).
        $this->cache->clear();

        $cacheDir = rtrim($this->rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($cacheDir)) {
            $output->writeln("<info>No cache directory:</info> {$cacheDir}");
            return Command::SUCCESS;
        }

        $removed = 0;
        $files = glob($cacheDir . DIRECTORY_SEPARATOR . '*') ?: [];
        foreach ($files as $file) {
            $base = basename($file);
            if ($base === '.gitkeep') {
                continue;
            }
            if (is_file($file) && @unlink($file)) {
                $removed++;
                $output->writeln("Removed {$file}");
            }
        }

        $output->writeln("<info>Cache cleared.</info> Removed {$removed} file(s).");
        return Command::SUCCESS;
    }
}

