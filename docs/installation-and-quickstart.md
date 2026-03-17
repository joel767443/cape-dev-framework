# Installation + quickstart

## Requirements

- PHP **8.1+**
- Composer

## Backend (PHP)

From the repo root:

```bash
composer install
php -S localhost:8001 -t public public/index.php
```

The HTTP entrypoint is `index.php`. It loads routes from `routes/api.php` and runs `WebApp\Application`.

### Configuration

Configuration is loaded from `config/*.php` and environment variables (loaded via Dotenv in `src/Application.php`).

Common env vars:

- `APP_ENV` (default: `production`)
- `APP_DEBUG` (truthy strings: `1`, `true`, `yes`, `on`)
- `DB_CONNECTION` (default: `sqlite`)
- `DB_SQLITE_PATH` (default: resolved to `src/Database/cape-dev.sqlite`)

## Sanity checks

- Open `http://localhost:8001/` and confirm the landing page loads.
- Open `http://localhost:8001/docs` and confirm docs load.
- List backend routes:

```bash
php run route:list
```

