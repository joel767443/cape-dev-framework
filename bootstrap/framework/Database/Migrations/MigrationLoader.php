<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Database\Migrations;

use App\Database\Migrations\MigrationInterface;

/**
 *
 */
final class MigrationLoader
{
    /**
     * @param string $migrationsPath
     */
    public function __construct(private readonly string $migrationsPath)
    {
    }

    /**
     * @return array<string, class-string<MigrationInterface>>
     */
    public function discover(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob(rtrim($this->migrationsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files, SORT_STRING);

        $out = [];
        foreach ($files as $file) {
            $base = basename($file);
            if (!preg_match('/^(\\d{4}_\\d{2}_\\d{2}_\\d{6})_(.+)\\.php$/', $base, $m)) {
                continue;
            }

            $class = $m[2];
            $fqcn = 'App\\Database\\Migrations\\' . $class;

            require_once $file;

            if (class_exists($fqcn) && is_subclass_of($fqcn, MigrationInterface::class)) {
                $out[$base] = $fqcn;
            }
        }

        return $out;
    }
}

