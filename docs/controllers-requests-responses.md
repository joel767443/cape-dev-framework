# Controllers / requests / responses

## Controllers

Controllers are plain PHP classes, typically in `src/Http/Controllers`.

Routes bind controllers using `[ControllerClass::class, 'method']` in `routes/api.php`. The router stores these as `"Class::method"` under the Symfony route default `_controller`.

## Controller method signature (argument injection)

When the kernel invokes a controller (`src/Http/Kernel.php`), it resolves arguments in this order:

- If a parameter type-hints `Symfony\Component\HttpFoundation\Request`, it receives the current request.
- If a parameter type-hints a subclass of `App\Http\Requests\FormRequest`, it will be:
  - instantiated (via the DI container if available)
  - bound to the current request (`FormRequest::setRequest()`)
  - validated (see “Validation”)
  - passed into the controller
- If a parameter type-hints any other class and the container exists, it is resolved from the container.
- Otherwise the default value is used (if available), else the kernel throws a 500.

## Responses

Controllers **must return** an instance of `Symfony\Component\HttpFoundation\Response`.

Most JSON APIs should return `Symfony\Component\HttpFoundation\JsonResponse`.

### Error responses

Unhandled exceptions are converted to responses by:

- `WebApp\Http\Middleware\ExceptionHandlingMiddleware`
- `WebApp\Http\Exception\ExceptionHandler`

For `/api/*` paths (or `Accept: application/json`), errors are returned as JSON:

```json
{
  "success": false,
  "status": 422,
  "code": 422,
  "message": "Validation failed",
  "details": { "errors": { "field": ["..."] } }
}
```

## Outbound HTTP (Guzzle)

This project ships with a small wrapper around Guzzle so you can type-hint a single service from the container:

- `WebApp\Http\Client\HttpClient`

Configuration:

- `HTTP_BASE_URI` → `config('http.base_uri')`
- `HTTP_TIMEOUT` → `config('http.timeout')`

