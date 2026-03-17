<?php

use WebApp\Config\ConfigRepository;
use WebApp\View\ViewRenderer;
use Psr\Container\ContainerInterface;

if (!function_exists('config')) {
    /**
     * @param mixed $default
     */
    function config(string $key, mixed $default = null): mixed
    {
        if (!isset($GLOBALS['__webapp_config']) || !$GLOBALS['__webapp_config'] instanceof ConfigRepository) {
            return $default;
        }

        return $GLOBALS['__webapp_config']->get($key, $default);
    }
}

if (!function_exists('app')) {
    function app(?string $id = null): mixed
    {
        $container = $GLOBALS['__webapp_container'] ?? null;
        if (!$container instanceof ContainerInterface) {
            if ($id === null) {
                return null;
            }

            throw new RuntimeException('Application container not available');
        }

        return $id === null ? $container : $container->get($id);
    }
}

if (!function_exists('view')) {
    /**
     * Render a Blade view to a string.
     *
     * @param array<string, mixed> $data
     */
    function view(string $name, array $data = []): string
    {
        /** @var ViewRenderer $renderer */
        $renderer = app(ViewRenderer::class);
        return $renderer->render($name, $data);
    }
}

