<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

// Support PHP's built-in server: let it serve existing files (e.g. /logo.png).
if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = is_string($path) ? $path : '/';
    $path = urldecode($path);
    $candidate = __DIR__ . $path;
    $file = realpath($candidate);

    // Ensure the resolved path stays within /public.
    $publicRoot = rtrim(realpath(__DIR__) ?: __DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (
        $path !== '/'
        && is_string($file)
        && str_starts_with($file, $publicRoot)
        && is_file($file)
    ) {
        return false;
    }
}

use WebApp\Application;

/** @var Application $app */
$app = require dirname(__DIR__) . '/bootstrap/app.php';

// Load routes.
$router = $app->router;
require dirname(__DIR__) . '/routes/api.php';
if (is_file(dirname(__DIR__) . '/routes/web.php')) {
    require dirname(__DIR__) . '/routes/web.php';
}

$app->run();
