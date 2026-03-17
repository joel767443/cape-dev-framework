# Upgrade guide

This guide covers upgrades for the **whole app** (backend + frontend + ops/config).

## What counts as a breaking change

- **API routes**: changing paths/methods in `routes/api.php`
- **Response contracts**: changing JSON envelope fields (`success`, `code`, `message`, `data`, error shape)
- **Validation behavior**: changes in `FormRequest` auto-validation, constraint semantics, or error formatting
- **Config keys / env vars**: changes in `config/*.php` keys or the environment variable names they read
- **Migrations**: changes to the migration discovery location or execution semantics (`php run migrate`)
- **CLI commands**: renamed commands, removed options, changed output formats
- **Frontend expectations**: changing `apiBaseUrl` shape, CORS behavior, or required headers

## Before you upgrade

1. Review `CHANGELOG.md` for breaking changes.
2. Check any local environment variables (especially `DB_SQLITE_PATH`, `APP_DEBUG`, `REDIS_DSN`, queue/cache vars).
3. Run tests:

```bash
composer install
./vendor/bin/phpunit
```

4. If the DB schema changed, run migrations:

```bash
php run migrate
```

## Common upgrade scenarios

### Backend routing changes

- Re-run `php run route:list` to confirm the registered route table matches your expectations.
- Confirm `index.php` and console both load `routes/api.php` (they should).

### Validation changes (FormRequest)

- If controller methods start type-hinting `App\Requests\FormRequest` subclasses, remember they will be auto-validated by the kernel and may now return 422 errors for invalid payloads.

### Frontend API base URL changes

- `front-end/src/config.js` should typically target `http://localhost:8001/api` in local dev.

