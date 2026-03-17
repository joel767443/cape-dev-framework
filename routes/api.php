<?php

use WebApp\Http\Controllers\ItemsController;
use WebApp\Http\Controllers\AuthController;
use WebApp\Http\Controllers\DocsController;

/**
 * @var \WebApp\Router $router
 */

$router->post('/api/auth/token', [AuthController::class, 'token']);

$router->add('GET', '/api/secure/ping', [AuthController::class, 'ping'], ['middleware' => ['auth_jwt']]);

$router->get('/docs', [DocsController::class, 'index']);
$router->add('GET', '/docs/{page}', [DocsController::class, 'show'], ['where' => ['page' => '.+']]);

$router->get('/api/items', [ItemsController::class, 'index']);
$router->get('/api/item', [ItemsController::class, 'show']);
$router->post('/api/items/create', [ItemsController::class, 'create']);
$router->get('/api/items/delete', [ItemsController::class, 'delete']);
$router->post('/api/items/update', [ItemsController::class, 'update']);
$router->post('/api/items/validate', [ItemsController::class, 'validateExample']);

