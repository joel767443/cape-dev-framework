<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Router
 */
class Router
{
    /**
     * @var RouteCollection
     */
    protected RouteCollection $routes;

    /**
     * Route group stack.
     *
     * Each item:
     * - prefix: string
     * - name_prefix: string
     * - middleware: array
     *
     * @var array<int, array{prefix: string, name_prefix: string, middleware: array}>
     */
    protected array $groupStack = [];

    /**
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->groupStack[] = ['prefix' => '', 'name_prefix' => '', 'middleware' => []];
    }

    /**
     * @param array{prefix?: string, name_prefix?: string, middleware?: array} $opts
     * @param callable(self): void $callback
     */
    public function group(array $opts, callable $callback): void
    {
        $parent = $this->currentGroup();
        $prefix = $this->joinPath($parent['prefix'], (string)($opts['prefix'] ?? ''));
        $namePrefix = $this->joinName($parent['name_prefix'], (string)($opts['name_prefix'] ?? ''));
        $middleware = array_values(array_merge($parent['middleware'], (array)($opts['middleware'] ?? [])));

        $this->groupStack[] = [
            'prefix' => $prefix,
            'name_prefix' => $namePrefix,
            'middleware' => $middleware,
        ];

        try {
            $callback($this);
        } finally {
            array_pop($this->groupStack);
        }
    }

    /**
     * @param string $path
     * @param array $callback
     * @return void
     */
    public function get(string $path, array $callback)
    {
        $this->add('GET', $path, $callback);
    }

    /**
     * @param string $path
     * @param array $callback
     * @return void
     */
    public function post(string $path, array $callback): void
    {
        $this->add('POST', $path, $callback);
    }

    public function put(string $path, array $callback): void
    {
        $this->add('PUT', $path, $callback);
    }

    public function delete(string $path, array $callback): void
    {
        $this->add('DELETE', $path, $callback);
    }

    /**
     * @param array{as?: string, middleware?: array, where?: array<string, string>} $opts
     */
    public function add(string $method, string $path, array $callback, array $opts = []): void
    {
        $methods = [strtoupper($method)];
        $this->addRoute($path, $callback, $methods, $opts);
    }

    /**
     * @return RouteCollection
     */
    public function getRouteCollection(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * Register a route. Controller is stored as "Class::method".
     *
     * @param string $path
     * @param array{0: class-string, 1: string} $callback
     * @param string[] $methods
     * @return void
     */
    /**
     * @param array{as?: string, middleware?: array, where?: array<string, string>} $opts
     */
    protected function addRoute(string $path, array $callback, array $methods, array $opts = []): void
    {
        $group = $this->currentGroup();

        [$controllerClass, $method] = $callback;
        $controller = $controllerClass . '::' . $method;

        $fullPath = $this->joinPath($group['prefix'], $path);

        $routeMiddleware = array_values(array_merge($group['middleware'], (array)($opts['middleware'] ?? [])));
        $defaults = [
            '_controller' => $controller,
            '_middleware' => $routeMiddleware,
        ];

        $requirements = (array)($opts['where'] ?? []);

        $route = new Route(
            $fullPath,
            $defaults,
            $requirements,
            [],
            '',
            [],
            $methods
        );

        $explicitName = (string)($opts['as'] ?? '');
        $routeName = $explicitName !== ''
            ? $this->joinName($group['name_prefix'], $explicitName)
            : $this->joinName($group['name_prefix'], $this->routeName($methods[0] ?? 'ANY', $fullPath));

        $this->routes->add($routeName, $route);
    }

    protected function routeName(string $method, string $path): string
    {
        $method = strtolower($method);
        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', trim($path, '/')) ?: 'root';
        return $method . '_' . trim($name, '_');
    }

    /**
     * @return array{prefix: string, name_prefix: string, middleware: array}
     */
    protected function currentGroup(): array
    {
        return $this->groupStack[count($this->groupStack) - 1];
    }

    protected function joinPath(string $base, string $append): string
    {
        $base = rtrim($base, '/');
        $append = ltrim($append, '/');
        if ($base === '') {
            return '/' . $append;
        }
        if ($append === '') {
            return $base;
        }
        return $base . '/' . $append;
    }

    protected function joinName(string $base, string $append): string
    {
        $base = trim($base);
        $append = trim($append);
        if ($base === '') {
            return $append;
        }
        if ($append === '') {
            return $base;
        }
        return rtrim($base, '.') . '.' . ltrim($append, '.');
    }
}