<?php

use WebApp\Application;
use WebApp\Http\Controllers\ItemsController;
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle pre-flight request. Respond successfully to OPTIONS requests.
    header("HTTP/1.1 200 OK");
    exit();
}

require_once "autoload.php";

$app = new Application(dirname(__DIR__));

$app->router->get('/api/items', [ItemsController::class, 'index']);
$app->router->get('/api/item', [ItemsController::class, 'show']);
$app->router->post('/api/items/create', [ItemsController::class, 'create']);
$app->router->get('/api/items/delete', [ItemsController::class, 'delete']);
$app->router->post('/api/items/update', [ItemsController::class, 'update']);

$app->run();
