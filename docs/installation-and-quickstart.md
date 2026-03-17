# Installation + quickstart

## Requirements

- PHP **8.1+**
- Composer
- Node.js + npm (for the Vue frontend)

## Backend (PHP)

From the repo root:

```bash
composer install
php -S localhost:8001 index.php
```

The HTTP entrypoint is `index.php`. It loads routes from `routes/api.php` and runs `WebApp\Application`.

### Configuration

Configuration is loaded from `config/*.php` and environment variables (loaded via Dotenv in `src/Application.php`).

Common env vars:

- `APP_ENV` (default: `production`)
- `APP_DEBUG` (truthy strings: `1`, `true`, `yes`, `on`)
- `DB_CONNECTION` (default: `sqlite`)
- `DB_SQLITE_PATH` (default: resolved to `src/Database/cape-dev.sqlite`)

## Frontend (Vue 3 + Vite)

```bash
cd front-end
npm install
cp src/config.example.js src/config.js
```

Set `apiBaseUrl` in `front-end/src/config.js` to:

- `http://localhost:8001/api`

Then run:

```bash
npm run dev
```

## Sanity checks

- Open the frontend dev server and confirm it can reach the API.
- List backend routes:

```bash
php bin/console route:list
```

