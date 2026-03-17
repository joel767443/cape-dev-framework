<?php

namespace WebApp\Console\Support;

final class CodeWriter
{
    public function __construct(private readonly string $rootPath)
    {
    }

    public function rootPath(): string
    {
        return $this->rootPath;
    }

    public function ensureDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException("Could not create directory: {$dir}");
        }
    }

    public function writeFile(string $path, string $contents, bool $force = false): void
    {
        $dir = dirname($path);
        $this->ensureDir($dir);

        if (!$force && is_file($path)) {
            throw new \RuntimeException("File already exists: {$path}");
        }

        $bytes = file_put_contents($path, $contents);
        if ($bytes === false) {
            throw new \RuntimeException("Could not write file: {$path}");
        }
    }
}

