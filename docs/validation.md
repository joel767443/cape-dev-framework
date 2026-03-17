# Validation

This codebase uses **Symfony Validator + FormRequest** for request validation.

## FormRequest validation (recommended)

- Base class: `App\Http\Requests\FormRequest`
- Validator wrapper: `WebApp\Validation\RequestValidator`
- Validator engine: `symfony/validator`

### How it works

If a controller action type-hints a `FormRequest`, the HTTP kernel will:

1. Instantiate the request object.
2. Bind the current HTTP request (`setRequest()`).
3. Validate its constraints (`validateResolved()`), which calls `RequestValidator`.
4. If valid: pass the `FormRequest` into your controller.
5. If invalid: throw an `HttpException(422, ...)` which becomes a JSON error response.

### Example

See `app/Http/Requests/ValidateItemRequest.php` and the route `POST /api/items/validate` in `routes/api.php`.

Your `FormRequest` returns Symfony constraints:

- `Symfony\Component\Validator\Constraints\Collection`
- `NotBlank`, `Type`, `Length`, etc.

### DB-backed constraints (Exists/Unique)

The backend also provides DB-backed validators wired through the container:

- `WebApp\Validation\Constraints\Exists`
- `WebApp\Validation\Constraints\Unique`

They are constructed with the shared Illuminate `ConnectionInterface` in `src/Providers/ValidationServiceProvider.php`.

