# DI container + providers

## Container

The DI container is built by `src/Container/ContainerFactory.php` using **PHP-DI**:

- autowiring enabled
- attributes disabled

`WebApp\Application` stores the container in `$GLOBALS['__webapp_container']` and exposes it via `Application::container()`.

## Service providers

Providers implement `src/Container/ServiceProviderInterface.php` and are used to:

- register container definitions (`register()`)
- run boot-time wiring (`boot()`)

`src/Application.php` builds the container with these providers:

- `src/Providers/HttpServiceProvider.php` (exception handler + middleware + middleware aliases)
- `src/Providers/ValidationServiceProvider.php` (Symfony Validator + RequestValidator + DB-backed validators)
- `src/Providers/DatabaseServiceProvider.php` (Illuminate DB + `ConnectionInterface`)
- `src/Providers/LoggingServiceProvider.php`
- `src/Providers/EventsServiceProvider.php`
- `src/Providers/CacheServiceProvider.php`
- `src/Providers/QueueServiceProvider.php`
- `src/Providers/ViewServiceProvider.php`
- `src/Providers/RoutingServiceProvider.php` (currently a no-op; routes are loaded by entrypoints)

## Adding a new service

1. Create a provider (or extend an existing one) under `src/Providers/`.
2. Add bindings in `register()` using PHP-DI definitions.
3. Add any runtime wiring in `boot()` (e.g., registering middleware aliases).
4. Ensure the provider is included in `src/Application.php` container build list.

