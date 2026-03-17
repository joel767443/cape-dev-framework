<?php

use WebApp\Http\Controllers\ItemsController;

/**
 * @var \WebApp\Router $router
 */

$router->get('/api/items', [ItemsController::class, 'index']);
$router->get('/api/item', [ItemsController::class, 'show']);
$router->post('/api/items/create', [ItemsController::class, 'create']);
$router->get('/api/items/delete', [ItemsController::class, 'delete']);
$router->post('/api/items/update', [ItemsController::class, 'update']);

