<?php

namespace WebApp\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use WebApp\Http\Exception\HttpException;
use WebApp\Http\Exception\NotFoundHttpException;
use WebApp\Http\Middleware\MiddlewareInterface;

final class Kernel
{
    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        private readonly RouteCollection $routes,
        private readonly array $middleware = []
    ) {
    }

    public function handle(Request $request): Response
    {
        $core = function (Request $request): Response {
            try {
                return $this->dispatch($request);
            } catch (\Throwable $e) {
                return $this->renderException($e);
            }
        };

        $pipeline = array_reduce(
            array_reverse($this->middleware),
            /**
             * @param callable(Request): Response $next
             */
            function (callable $next, MiddlewareInterface $mw): callable {
                return fn (Request $request): Response => $mw->process($request, $next);
            },
            $core
        );

        return $pipeline($request);
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

        $controller = $parameters['_controller'] ?? null;
        if ($controller === null) {
            throw new HttpException(500, 'Controller not configured');
        }

        $callable = $this->normalizeController($controller);
        $response = $callable($request);

        if (!$response instanceof Response) {
            throw new HttpException(500, 'Controller must return a Response');
        }

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
                    $instance = new $class();
                    $response = $instance->{$method}($request);
                    return $response;
                };
            }
        }

        if (is_string($controller) && str_contains($controller, '::')) {
            [$class, $method] = explode('::', $controller, 2);
            return function (Request $request) use ($class, $method): Response {
                $instance = new $class();
                $response = $instance->{$method}($request);
                return $response;
            };
        }

        return fn (): Response => throw new HttpException(500, 'Invalid controller');
    }

    private function renderException(\Throwable $e): Response
    {
        if ($e instanceof HttpException) {
            return new JsonResponse(
                [
                    'success' => false,
                    'code' => $e->statusCode,
                    'message' => $e->getMessage(),
                    'data' => $e->details,
                ],
                $e->statusCode
            );
        }

        return new JsonResponse(
            [
                'success' => false,
                'code' => 500,
                'message' => 'Server Error',
                'data' => [],
            ],
            500
        );
    }
}

