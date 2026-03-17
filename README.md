# cape-dev

Full-stack CRUD app: a lightweight custom PHP backend (routing + middleware + DI + validation + migrations) and a Vue 3 SPA frontend.

## Installation + quickstart

### Backend (PHP)

```bash
composer install

# start HTTP server (from repo root)
php -S localhost:8001 index.php
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
| GET | `/api/items` | List items (currently returns empty array) |
| GET | `/api/item` | Show item (currently 501) |
| POST | `/api/items/create` | Create item (currently 501) |
| POST | `/api/items/update` | Update item (currently 501) |
| GET | `/api/items/delete` | Delete item (currently 501) |
| POST | `/api/items/validate` | Validation example (returns validated payload or 422) |

## Backend architecture (source of truth)

- **Entrypoint**: `index.php` loads routes from `routes/api.php` and runs `WebApp\Application`.
- **HTTP kernel**: `src/Http/Kernel.php` matches routes (Symfony Routing), runs middleware, invokes controllers, and enforces that controllers return a `Symfony\Component\HttpFoundation\Response`.
- **Middleware**: global middleware is wired in `src/Application.php`. Route middleware is attached per route (or via `Router::group()`) and resolved via `src/Http/Middleware/MiddlewareRegistry.php`.
- **Validation**: request validation is Symfony Validator via `App\Http\Requests\FormRequest` + `WebApp\Validation\RequestValidator`. If a controller action type-hints a `FormRequest`, it is automatically validated by the kernel before the controller runs.
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
