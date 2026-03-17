<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use WebApp\Http\Requests\Request;
use WebApp\Http\Responses\Response;

/**
 * Class Router
 */
class Router
{
    /**
     * @var Request
     */
    public Request $request;
    /**
     * @var Response
     */
    public Response $response;

    /**
     * @var RouteCollection
     */
    protected RouteCollection $routes;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->routes = new RouteCollection();
    }

    /**
     * @param string $path
     * @param array $callback
     * @return void
     */
    public function get(string $path, array $callback)
    {
        $this->addRoute($path, $callback, ['GET']);
    }

    /**
     * @param string $path
     * @param array $callback
     * @return void
     */
    public function post(string $path, array $callback): void
    {
        $this->addRoute($path, $callback, ['POST']);
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
    protected function addRoute(string $path, array $callback, array $methods): void
    {
        [$controllerClass, $method] = $callback;
        $controller = $controllerClass . '::' . $method;

        $route = new Route(
            $path,
            ['_controller' => $controller],
            [],
            [],
            '',
            [],
            $methods
        );

        $routeName = $this->routeName($methods[0] ?? 'ANY', $path);
        $this->routes->add($routeName, $route);
    }

    protected function routeName(string $method, string $path): string
    {
        $method = strtolower($method);
        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', trim($path, '/')) ?: 'root';
        return $method . '_' . trim($name, '_');
    }
}