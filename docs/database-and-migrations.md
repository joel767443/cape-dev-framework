# Database + migrations

## Database configuration

Database config lives in `config/database.php` and is driven by environment variables:

- `DB_CONNECTION` (default: `sqlite`)
- `DB_SQLITE_PATH` (optional)

If `DB_SQLITE_PATH` is not set, the SQLite database defaults to:

- `src/Database/cape-dev.sqlite` (resolved relative to the repo root)

## Illuminate / Eloquent wiring

`src/Providers/DatabaseServiceProvider.php` wires:

- `Illuminate\Database\Capsule\Manager` as a global capsule
- `Illuminate\Database\ConnectionInterface` for DI

This means you can depend on `ConnectionInterface` in your services/validators.

## Migrations

### Location

Migrations are discovered from:

- `app/Database/Migrations`

### Running migrations

```bash
php run migrate
```

Dry-run pending migrations:

```bash
php run migrate --dry-run
```

### Creating migrations

```bash
php run make:migration create_widgets_table
```

The command writes a migration class under `app/Database/Migrations`.

## Doctrine ORM (optional)

This project also supports Doctrine ORM as an **opt-in** alternative to Eloquent.

- **Entities path**: `app/Entities` (attributes mapping)
- **Schema update command**:

```bash
php run doctrine:schema:update --dump-sql
php run doctrine:schema:update --force
```

Configuration is under `config/database.php` → `database.doctrine.*` and environment variables like `DOCTRINE_SQLITE_PATH`.

## Carbon + UUID helpers

This repo provides two helpers (see `src/Support/helpers.php`):

- `now()` returns a `CarbonImmutable`
- `uuid()` returns a UUID v4 string

For Eloquent models that should use UUID primary keys, you can apply the trait:

- `App\Models\Concerns\UsesUuid`

