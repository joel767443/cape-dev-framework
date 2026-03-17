<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Config;

/**
 *
 */
final class ConfigLoader
{
    /**
     * Load PHP config files from a directory.
     *
     * Each `*.php` file should return an array.
     *
     * @return array<string, mixed>
     */
    public function loadDir(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $items = [];
        $files = glob(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files);

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $value = include $file;
            if (is_array($value)) {
                $items[$key] = $value;
            }
        }

        return $items;
    }
}

