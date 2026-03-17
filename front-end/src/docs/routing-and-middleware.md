# Routing + middleware

- Routes are registered in `routes/api.php`.
- The HTTP kernel is `src/Http/Kernel.php` (Symfony Routing matcher + middleware pipeline).
- Global middleware is wired in `src/Application.php`.
- Route middleware aliases are registered in `src/Providers/HttpServiceProvider.php`.

