# Authentication (JWT)

- Token endpoint: `POST /api/auth/token`
- Protected example: `GET /api/secure/ping` (middleware alias `auth_jwt`)

Set `JWT_SECRET` (or `APP_KEY`) to enable token issuance/verification.

