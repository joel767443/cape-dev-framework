<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

// Prefer Composer autoload (Symfony components, PSR-4).
$composerAutoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(function (string $class_name): void {

    // Map the namespace to the src folder
    $namespace_mapping = [
        'WebApp\\' => '/bootstrap/framework',
        'App\\' => '/app',
    ];

    foreach ($namespace_mapping as $namespace => $directory) {

        if (
            strpos($class_name, $namespace = trim($namespace, '\\')) !== 0
            || (!$directory = realpath(__DIR__ . DIRECTORY_SEPARATOR . trim($directory, DIRECTORY_SEPARATOR)))
        ) {
            continue; // Class name doesn't match or the directory doesn't exist
        }

        // Require the file
        $class_file = $directory . str_replace([$namespace, '\\'], ['', DIRECTORY_SEPARATOR], $class_name) . '.php';
        if (file_exists($class_file)) {
            require_once $class_file;
        }
    }
});