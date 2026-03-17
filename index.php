<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

use WebApp\Application;

require_once "autoload.php";

$app = new Application(dirname(__DIR__));

// Load routes.
$router = $app->router;
require __DIR__ . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'api.php';

$app->run();
