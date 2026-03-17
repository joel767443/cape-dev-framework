@extends('layouts.app')

@section('title', 'Cape Dev PHP Framework')

@section('content')
      <div class="py-4 py-lg-5">
        <div class="row align-items-center g-4">
          <div class="col-12 col-lg-7">
            <div class="mb-3">
              <div class="d-flex align-items-center gap-2">
                <img src="/logo.png" alt="cape-dev logo" width="28" height="28" style="border-radius: 6px" />
                <span class="badge text-bg-dark">Cape Dev PHP Framework</span>
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
              <a class="btn btn-dark btn-lg" href="/docs/installation-and-quickstart">How to get started</a>
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

        <div class="row g-4 align-items-start">
          <div class="col-12 col-lg-6">
            <h2 class="h4 mb-3">How to</h2>
            <div class="card shadow-sm">
              <div class="card-body">
                <ol class="mb-3">
                  <li><code>composer install</code></li>
                  <li><code>php run dev</code></li>
                  <li>Open <code>/</code> for the landing page, and <code>/docs</code> for docs.</li>
                </ol>

                <div class="d-flex flex-wrap gap-2">
                  <a class="btn btn-outline-secondary" href="/docs/how-to">Backend developer guide</a>
                  <a class="btn btn-outline-secondary" href="/docs/routing-and-middleware">Routing + middleware</a>
                  <a class="btn btn-outline-secondary" href="/docs/controllers-requests-responses">Controllers + requests</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <h2 class="h4 mb-3">Relevant docs</h2>
            <div class="list-group shadow-sm">
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/installation-and-quickstart">
                <span>Installation + quickstart</span>
                <span class="text-secondary small">/docs/installation-and-quickstart</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/routing-and-middleware">
                <span>Routing + middleware</span>
                <span class="text-secondary small">/docs/routing-and-middleware</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/controllers-requests-responses">
                <span>Controllers / requests / responses</span>
                <span class="text-secondary small">/docs/controllers-requests-responses</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/validation">
                <span>Validation</span>
                <span class="text-secondary small">/docs/validation</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/database-and-migrations">
                <span>Database + migrations</span>
                <span class="text-secondary small">/docs/database-and-migrations</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/auth">
                <span>Authentication (JWT)</span>
                <span class="text-secondary small">/docs/auth</span>
              </a>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/docs/queue">
                <span>Queue</span>
                <span class="text-secondary small">/docs/queue</span>
              </a>
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
@endsection

