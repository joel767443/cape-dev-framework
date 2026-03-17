# Routing + middleware

## Where routes live

Routes are registered in `routes/api.php`:

- The HTTP front controller `index.php` requires this file.
- The console factory `src/Console/ConsoleApplicationFactory.php` also requires this file so `route:list` matches what HTTP serves.

## Router API (`WebApp\Router`)

`src/Router.php` provides a small, explicit API for registering routes:

- `get($path, [$controllerClass, $method])`
- `post($path, [$controllerClass, $method])`
- `put($path, [$controllerClass, $method])`
- `delete($path, [$controllerClass, $method])`
- `group(['prefix' => '/api', 'name_prefix' => 'api.', 'middleware' => [...]], fn (Router $r) => ...)`

Internally it builds a Symfony `RouteCollection` and stores:

- `_controller`: `"Class::method"`
- `_middleware`: an array of middleware entries (strings or instances)

## HTTP kernel (`WebApp\Http\Kernel`)

`src/Http/Kernel.php` is the request lifecycle:

1. Match the request path against the `RouteCollection` (Symfony Routing `UrlMatcher`).
2. Build the middleware pipeline:
   - Global middleware (wired in `src/Application.php`)
   - Route-specific middleware (from `_middleware`)
3. Resolve and invoke the controller.

If a route is not found, the kernel throws `WebApp\Http\Exception\NotFoundHttpException` which is rendered by the exception handler middleware.

## Global middleware

Global middleware is defined in `src/Application.php` and (currently) includes:

- `WebApp\Http\Middleware\ExceptionHandlingMiddleware`
- `WebApp\Http\Middleware\ErrorLoggingMiddleware`
- `WebApp\Http\Middleware\CorsMiddleware`

Ordering matters: exception handling should wrap everything.

## Route middleware + aliases

Routes can attach middleware by passing `['middleware' => ['aliasOrClass', ...]]` to `Router::add()` (or via `Router::group()` middleware).

Middleware entries can be:

- an alias name (resolved by `WebApp\Http\Middleware\MiddlewareRegistry`)
- a fully qualified middleware class name

Aliases are registered in `src/Providers/HttpServiceProvider.php` during provider boot.

