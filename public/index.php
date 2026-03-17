<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

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
