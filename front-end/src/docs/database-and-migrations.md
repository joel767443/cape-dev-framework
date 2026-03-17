# Database + migrations

## Eloquent (default)

- Config: `config/database.php`
- Provider: `src/Providers/DatabaseServiceProvider.php`
- Migrate: `php bin/console migrate`

## Doctrine ORM (optional)

- Entities: `app/Entities`
- Schema update:

```bash
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```

## Carbon + UUID

- `now()` → CarbonImmutable
- `uuid()` → UUID v4 string
- Eloquent UUID trait: `App\Models\Concerns\UsesUuid`

