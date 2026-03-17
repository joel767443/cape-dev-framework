<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>cape-dev PHP Framework</title>

    <link rel="icon" href="/logo.png" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <style>
      pre {
        background: #0b1020;
        color: #e8edf6;
        border-radius: 0.5rem;
        padding: 1rem;
        overflow: auto;
      }
    </style>
  </head>
  <body>
    <div class="container mt-3">
      <div class="py-4 py-lg-5">
        <div class="row align-items-center g-4">
          <div class="col-12 col-lg-7">
            <div class="mb-3">
              <div class="d-flex align-items-center gap-2">
                <img src="/logo.png" alt="cape-dev logo" width="28" height="28" style="border-radius: 6px" />
                <span class="badge text-bg-dark">cape-dev PHP Framework</span>
              </div>
            </div>

            <h1 class="display-5 fw-semibold mb-3">
              A small, pragmatic PHP framework for building APIs fast.
            </h1>
            <p class="lead text-secondary mb-4">
              Batteries-included routing, requests/responses, validation, and a clean
              structure you can grow into.
            </p>

            <div class="d-flex flex-wrap gap-2">
              <a class="btn btn-outline-secondary btn-lg" href="/docs">Read docs</a>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
              <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Quick start</span>
                <span class="badge text-bg-secondary">composer</span>
              </div>
              <div class="card-body">
                <pre class="mb-0"><code># backend
composer install
php run dev</code></pre>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-5" />

        <div class="row g-3 g-lg-4">
          <div class="col-12 col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h2 class="h5">Batteries included</h2>
                <p class="text-secondary mb-0">
                  Symfony Routing + HttpFoundation, PHP-DI, Symfony Validator, Monolog,
                  cache/queue, migrations.
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h2 class="h5">New integrations</h2>
                <p class="text-secondary mb-0">
                  Guzzle outbound HTTP, optional Doctrine ORM, optional Symfony Messenger,
                  JWT auth endpoints + middleware.
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h2 class="h5">Dev experience</h2>
                <p class="text-secondary mb-0">
                  Symfony Console commands, PHPUnit + Pest, Dotenv, Carbon <code>now()</code>, UUID <code>uuid()</code>.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-5 text-secondary small">
          <div>Tip: use <code>php run route:list</code> to see backend routes.</div>
        </div>
      </div>
    </div>
  </body>
</html>

