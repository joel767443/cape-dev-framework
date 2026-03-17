# Authentication (JWT)

This project implements **JWT-based auth** for API routes.

## Configuration

Environment variables:

- `JWT_SECRET` (recommended) or `APP_KEY`
- `JWT_ISSUER` (default: `webapp`)
- `JWT_TTL` seconds (default: `3600`)

## Get a token

Request:

```bash
curl -X POST http://localhost:8001/api/auth/token \
  -H 'Content-Type: application/json' \
  -d '{"sub":"demo","role":"user"}'
```

Response:

```json
{ "success": true, "data": { "token": "..." } }
```

## Protect routes

Routes can be protected by attaching middleware alias `auth_jwt` (see `src/Providers/HttpServiceProvider.php`).

Example:

- `GET /api/secure/ping` is protected and requires:

```bash
curl http://localhost:8001/api/secure/ping -H "Authorization: Bearer <token>"
```

The decoded claims are available on the request as attribute `auth`.

