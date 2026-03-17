# Testing guide

## Test runner

This repo uses PHPUnit (see `phpunit.xml`).

Run all tests from the repo root:

```bash
./vendor/bin/phpunit
```

## How tests are structured

Tests live in `tests/` and generally:

- require `autoload.php`
- instantiate `WebApp\Application`
- load routes from `routes/api.php` (for HTTP-kernel level tests)

Examples:

- `tests/HttpKernelTest.php`: creates a Symfony `Request`, passes it to `Application::handle()`, and asserts the returned `Symfony\Component\HttpFoundation\Response`.
- `tests/ViewRendererTest.php`: uses the global `view()` helper to render a Blade template.

## Writing new tests

If you’re writing a request/route test:

1. Instantiate the app.
2. Load `routes/api.php`.
3. Use `Symfony\Component\HttpFoundation\Request::create(...)`.
4. Assert against the returned `Response` object (status, JSON content-type, payload, etc).

