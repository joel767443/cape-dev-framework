# Queue (custom + Messenger)

This repo currently supports **two** ways to run background work:

## 1) Custom Redis-backed queue

Implemented in `src/Queue/*` and wired by `src/Providers/QueueServiceProvider.php`.

Commands:

```bash
php bin/console queue:dispatch App/Jobs/LogMessageJob
php bin/console queue:run
php bin/console queue:work
```

## 2) Symfony Messenger (optional)

Messenger is wired by `src/Providers/MessengerServiceProvider.php`.

Transport selection (env var `MESSENGER_TRANSPORT`):

- `sync` (default): messages are handled immediately (no queue)
- `in_memory`: process-local queue (useful for dev/tests)

Consume messages:

```bash
php bin/console messenger:consume --limit=10
```

Note: Redis transport requires the PHP `ext-redis` extension, which is not assumed by this repo.

