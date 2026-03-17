<?php

namespace WebApp\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use WebApp\Http\Exception\HttpException;
use WebApp\Http\Exception\NotFoundHttpException;
use WebApp\Http\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use WebApp\Http\Middleware\MiddlewareRegistry;
use WebApp\Events\Http\ControllerResolved;
use WebApp\Events\Http\RequestReceived;
use WebApp\Events\Http\ResponseReady;

final class Kernel
{
    /**
     * @param MiddlewareInterface[] $globalMiddleware
     */
    public function __construct(
        private readonly RouteCollection $routes,
        private readonly array $globalMiddleware = [],
        private readonly ?ContainerInterface $container = null,
        private readonly ?MiddlewareRegistry $registry = null,
        private readonly ?EventDispatcherInterface $events = null
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->events?->dispatch(new RequestReceived($request));
        return $this->buildPipeline($this->globalMiddleware, fn (Request $r) => $this->dispatch($r))($request);
    }

    private function dispatch(Request $request): Response
    {
        $context = (new RequestContext())->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $parameters = $matcher->match($request->getPathInfo());
        } catch (ResourceNotFoundException) {
            throw new NotFoundHttpException();
        }

        foreach ($parameters as $key => $value) {
            if ($key !== '' && $key[0] !== '_') {
                $request->attributes->set($key, $value);
            }
        }

        $routeMiddleware = $parameters['_middleware'] ?? [];
        if (is_array($routeMiddleware) && $routeMiddleware !== []) {
            return $this->buildPipeline(
                $this->instantiateMiddlewareList($routeMiddleware),
                fn (Request $r) => $this->dispatchController($parameters, $r)
            )($request);
        }

        return $this->dispatchController($parameters, $request);
    }

    private function dispatchController(array $parameters, Request $request): Response
    {
        $controller = $parameters['_controller'] ?? null;
        if ($controller === null) {
            throw new HttpException(500, 'Controller not configured');
        }

        $this->events?->dispatch(new ControllerResolved($request, $controller));

        $callable = $this->normalizeController($controller);
        $response = $callable($request);

        if (!$response instanceof Response) {
            throw new HttpException(500, 'Controller must return a Response');
        }

        $this->events?->dispatch(new ResponseReady($request, $response));
        return $response;
    }

    /**
     * @return callable(Request): Response
     */
    private function normalizeController(mixed $controller): callable
    {
        if (is_callable($controller)) {
            return $controller;
        }

        if (is_array($controller) && count($controller) === 2) {
            [$class, $method] = $controller;
            if (is_string($class) && is_string($method)) {
                return function (Request $request) use ($class, $method): Response {
                    $instance = $this->container ? $this->container->get($class) : new $class();
                    $response = $instance->{$method}($request);
                    return $response;
                };
            }
        }

        if (is_string($controller) && str_contains($controller, '::')) {
            [$class, $method] = explode('::', $controller, 2);
            return function (Request $request) use ($class, $method): Response {
                $instance = $this->container ? $this->container->get($class) : new $class();
                $response = $instance->{$method}($request);
                return $response;
            };
        }

        return fn (): Response => throw new HttpException(500, 'Invalid controller');
    }

    /**
     * @param array<int, MiddlewareInterface> $middleware
     * @param callable(Request): Response $core
     * @return callable(Request): Response
     */
    private function buildPipeline(array $middleware, callable $core): callable
    {
        return array_reduce(
            array_reverse($middleware),
            function (callable $next, MiddlewareInterface $mw): callable {
                return fn (Request $request): Response => $mw->process($request, $next);
            },
            $core
        );
    }

    /**
     * @param array<int, string|MiddlewareInterface> $list
     * @return array<int, MiddlewareInterface>
     */
    private function instantiateMiddlewareList(array $list): array
    {
        $out = [];
        foreach ($list as $mw) {
            if ($mw instanceof MiddlewareInterface) {
                $out[] = $mw;
                continue;
            }

            if (!is_string($mw) || trim($mw) === '') {
                throw new HttpException(500, 'Invalid middleware entry');
            }

            $class = $this->registry ? $this->registry->resolve($mw) : $mw;
            $out[] = $this->container ? $this->container->get($class) : new $class();
        }

        return $out;
    }
}

