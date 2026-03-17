<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Http;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
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
use WebApp\Validation\RequestValidator;
use App\Requests\FormRequest;

/**
 *
 */
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
        private readonly ?EventDispatcherInterface $events = null,
        private readonly ?RequestValidator $validator = null
    ) {
    }

    /**
     * @param Request $request
     * @return Response
     */
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
                    $args = $this->resolveControllerArguments($instance, $method, $request);
                    return $instance->{$method}(...$args);
                };
            }
        }

        if (is_string($controller) && str_contains($controller, '::')) {
            [$class, $method] = explode('::', $controller, 2);
            return function (Request $request) use ($class, $method): Response {
                $instance = $this->container ? $this->container->get($class) : new $class();
                $args = $this->resolveControllerArguments($instance, $method, $request);
                return $instance->{$method}(...$args);
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

    /**
     * @param object $controller
     * @param string $method
     * @param Request $request
     * @return array<int, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function resolveControllerArguments(object $controller, string $method, Request $request): array
    {
        try {
            $ref = new ReflectionMethod($controller, $method);
        } catch (ReflectionException) {
            // Fall back to the legacy signature.
            return [$request];
        }

        $args = [];
        foreach ($ref->getParameters() as $param) {
            $type = $param->getType();
            $typeName = $type instanceof ReflectionNamedType ? $type->getName() : null;

            if ($typeName === Request::class || $typeName === 'Symfony\\Component\\HttpFoundation\\Request') {
                $args[] = $request;
                continue;
            }

            if (
                $typeName !== null
                && class_exists($typeName)
                && is_subclass_of($typeName, FormRequest::class)
                && $this->validator !== null
            ) {
                /** @var FormRequest $formRequest */
                $formRequest = $this->container ? $this->container->get($typeName) : new $typeName();
                $formRequest->setRequest($request);
                $formRequest->validateResolved($this->validator);
                $args[] = $formRequest;
                continue;
            }

            if ($typeName !== null && class_exists($typeName) && $this->container) {
                $args[] = $this->container->get($typeName);
                continue;
            }

            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }

            throw new HttpException(500, 'Cannot resolve controller argument: ' . $param->getName());
        }

        return $args;
    }
}

