<?php

use WebApp\Http\Controllers\ItemsController;
use WebApp\Http\Controllers\AuthController;

/**
 * @var \WebApp\Router $router
 */

$router->post('/api/auth/token', [AuthController::class, 'token']);

$router->group(['middleware' => ['auth_jwt']], function (\WebApp\Router $router): void {
    $router->get('/api/items', [ItemsController::class, 'index']);
});
$router->get('/api/item', [ItemsController::class, 'show']);
$router->post('/api/items/create', [ItemsController::class, 'create']);
$router->get('/api/items/delete', [ItemsController::class, 'delete']);
$router->post('/api/items/update', [ItemsController::class, 'update']);
$router->post('/api/items/validate', [ItemsController::class, 'validateExample']);

