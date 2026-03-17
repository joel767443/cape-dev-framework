<?php

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WebApp\Application;
use WebApp\Config\ConfigRepository;
use WebApp\View\ViewRenderer;
use Psr\Container\ContainerInterface;
use Carbon\CarbonImmutable;
use Ramsey\Uuid\Uuid;

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
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
    /**
     * @param string|null $id
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
     * @param string $name
     * @param array<string, mixed> $data
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function view(string $name, array $data = []): string
    {
        /** @var ViewRenderer $renderer */
        $renderer = app(ViewRenderer::class);
        return $renderer->render($name, $data);
    }
}

if (!function_exists('now')) {
    /**
     * @return CarbonImmutable
     */
    function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }
}

if (!function_exists('uuid')) {
    /**
     * @return string
     */
    function uuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}

if (!function_exists('base_path')) {
    /**
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        $root = Application::$ROOT_PATH ?? '';
        if ($root === '') {
            $root = getcwd() ?: '';
        }

        $path = ltrim($path, DIRECTORY_SEPARATOR);
        return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . $path;
    }
}

