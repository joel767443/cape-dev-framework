# Controllers / requests / responses

- Controllers return `Symfony\Component\HttpFoundation\Response` (usually `JsonResponse`).
- Controller action args are resolved by `src/Http/Kernel.php` (Request, FormRequest, and DI services).
- Outbound HTTP is available via `WebApp\Http\Client\HttpClient` (Guzzle wrapper).

