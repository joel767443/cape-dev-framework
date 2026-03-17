<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'cape-dev')</title>

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

      .md h1, .md h2, .md h3 { margin-top: 1.25rem; }
      .md h1 { font-size: 1.6rem; }
      .md h2 { font-size: 1.35rem; }
      .md h3 { font-size: 1.15rem; }
      .md code { word-break: break-word; }
      .md table { width: 100%; }
      .md table th, .md table td { padding: .5rem; border-bottom: 1px solid rgba(0,0,0,.1); }
    </style>
    @stack('head')
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
          <img src="/logo.png" alt="cape-dev logo" width="24" height="24" style="border-radius: 6px" />
          <span>cape-dev</span>
        </a>
        <div class="navbar-nav ms-auto">
          <a class="nav-link" href="/docs">Docs</a>
        </div>
      </div>
    </nav>

    <main class="container mt-3">
      @yield('content')
    </main>
  </body>
</html>

