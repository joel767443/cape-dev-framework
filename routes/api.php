<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
use WebApp\Http\Controllers\AuthController;
use WebApp\Http\Controllers\DocsController;

/**
 * @var \WebApp\Router $router
 */

$router->post('/api/auth/token', [AuthController::class, 'token']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);

$router->add('GET', '/api/secure/ping', [AuthController::class, 'ping'], ['middleware' => ['auth_jwt']]);

$router->get('/docs', [DocsController::class, 'index']);
$router->add('GET', '/docs/{page}', [DocsController::class, 'show'], ['where' => ['page' => '.+']]);
$router->get('/queue.md', [DocsController::class, 'queueMd']);

$router->post('/api/validate', [AuthController::class, 'validateExample']);

