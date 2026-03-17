# cape-dev

Full-stack CRUD app: a lightweight custom PHP backend (routing + middleware + DI + validation + migrations) and a Vue 3 SPA frontend.

## What’s included (backend)

- **Routing**: Symfony Routing (`symfony/routing`) via `routes/api.php` + `src/Http/Kernel.php`
- **HTTP layer**: Symfony HttpFoundation (`symfony/http-foundation`)
- **DI container**: PHP-DI (`php-di/php-di`) + service providers (`src/Providers/*`)
- **Validation**: Symfony Validator (`symfony/validator`) + `App\Requests\FormRequest`
- **Database**: Eloquent/Illuminate Database (`illuminate/database`) + migrations (`php bin/console migrate`)
- **ORM (optional)**: Doctrine ORM (`doctrine/orm`) with `php bin/console doctrine:schema:update`
- **Outbound HTTP**: Guzzle (`guzzlehttp/guzzle`) via `WebApp\Http\Client\HttpClient`
- **Auth**: JWT (`firebase/php-jwt`) middleware alias `auth_jwt` + token endpoint `POST /api/auth/token`
- **Queue**: custom Redis queue + **optional** Symfony Messenger (`symfony/messenger`)
- **Logging**: Monolog (`monolog/monolog`)
- **Testing**: PHPUnit + Pest
- **Extras**: Dotenv, Carbon (`now()`), UUID (`uuid()`)

## Installation + quickstart

### Start both backend + frontend

```bash
chmod +x bin/dev
./bin/dev
```

### One-word CLI (`php run`)

`bin/console` still works, but you can also run the console as:

```bash
php run
php run route:list
php run dev
```

### Backend (PHP)

```bash
composer install

# start HTTP server (from repo root)
php -S localhost:8001 public/index.php
```

### Frontend (Vue)

```bash
cd front-end
npm install
cp src/config.example.js src/config.js
```

Set `apiBaseUrl` in `front-end/src/config.js` to `http://localhost:8001/api`, then:

```bash
npm run dev
```

## API endpoints (current routes)

Routes are defined in `routes/api.php`.

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/auth/token` | Issue a JWT token |
| GET | `/api/secure/ping` | JWT-protected example endpoint |
| POST | `/api/validate` | Validation example (returns validated payload or 422) |

## Backend architecture (source of truth)

- **Entrypoint**: `public/index.php` loads routes from `routes/api.php` (and `routes/web.php`) and runs `WebApp\Application`.
- **HTTP kernel**: `src/Http/Kernel.php` matches routes (Symfony Routing), runs middleware, invokes controllers, and enforces that controllers return a `Symfony\Component\HttpFoundation\Response`.
- **Middleware**: global middleware is wired in `src/Application.php`. Route middleware is attached per route (or via `Router::group()`) and resolved via `src/Http/Middleware/MiddlewareRegistry.php`.
- **Validation**: request validation is Symfony Validator via `App\Requests\FormRequest` + `WebApp\Validation\RequestValidator`. If a controller action type-hints a `FormRequest`, it is automatically validated by the kernel before the controller runs.
- **Database**: configured in `config/database.php` and wired via `src/Providers/DatabaseServiceProvider.php` (Illuminate Database / Eloquent).
- **Migrations**: discovered in `app/Database/Migrations` and run via `php bin/console migrate`.

## CLI (bin/console)

```bash
php bin/console route:list
php bin/console make:request StoreItemRequest
php bin/console make:migration create_items_table
php bin/console migrate
```

## Docs

- [Docs index](docs/README.md)
- [Installation + quickstart](docs/installation-and-quickstart.md)
- [Routing + middleware](docs/routing-and-middleware.md)
- [Controllers/requests/responses](docs/controllers-requests-responses.md)
- [Validation](docs/validation.md)
- [Database + migrations](docs/database-and-migrations.md)
- [DI container + providers](docs/di-container-and-providers.md)
- [Testing](docs/testing.md)
- [Authentication (JWT)](docs/auth.md)
- [Queue](docs/queue.md)
- [Upgrade guide](docs/upgrade-guide.md)
- [Changelog](CHANGELOG.md)
