<script setup>
const installSnippet = `composer create-project raccoon/framework my-app
cd my-app
php -S localhost:8000 -t public`
</script>

<template>
  <div class="py-4 py-lg-5">
    <div class="row align-items-center g-4">
      <div class="col-12 col-lg-7">
        <div class="mb-3">
          <span class="badge text-bg-dark">Raccoon PHP Framework</span>
        </div>

        <h1 class="display-5 fw-semibold mb-3">
          A small, pragmatic PHP framework for building APIs fast.
        </h1>
        <p class="lead text-secondary mb-4">
          Batteries-included routing, requests/responses, validation, and a clean
          structure you can grow into.
        </p>

        <div class="d-flex flex-wrap gap-2">
          <RouterLink class="btn btn-success btn-lg" to="/items">View CRUD demo</RouterLink>
          <a class="btn btn-outline-secondary btn-lg" href="#" aria-disabled="true">Read docs</a>
          <a class="btn btn-outline-secondary btn-lg" href="#" aria-disabled="true">GitHub</a>
        </div>
      </div>

      <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">Quick start</span>
            <span class="badge text-bg-secondary">composer</span>
          </div>
          <div class="card-body">
            <pre class="mb-0"><code>{{ installSnippet }}</code></pre>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-5" />

    <div class="row g-3 g-lg-4">
      <div class="col-12 col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="h5">Clean structure</h2>
            <p class="text-secondary mb-0">
              A predictable layout for controllers, requests, responses, models,
              and repositories.
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="h5">API-first</h2>
            <p class="text-secondary mb-0">
              Designed around JSON responses and a smooth local dev loop.
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="h5">Extensible</h2>
            <p class="text-secondary mb-0">
              Add middleware, services, and packages without fighting the core.
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5 text-secondary small">
      <div>Tip: the CRUD demo talks to your API endpoints under <code>/items</code>.</div>
    </div>
  </div>
</template>

<style scoped>
pre {
  background: #0b1020;
  color: #e8edf6;
  border-radius: 0.5rem;
  padding: 1rem;
  overflow: auto;
}
</style>
