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

