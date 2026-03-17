<?php

namespace WebApp\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DocsController
{
    public function index(): Response
    {
        return $this->renderDoc('README.md');
    }

    public function show(Request $request): Response
    {
        $page = (string) $request->attributes->get('page', '');
        if ($page === '') {
            return $this->renderDoc('README.md');
        }

        // Allow "auth" -> "auth.md", "database-and-migrations.md", etc.
        if (!str_ends_with(strtolower($page), '.md')) {
            $page .= '.md';
        }

        return $this->renderDoc($page);
    }

    private function renderDoc(string $filename): Response
    {
        $root = (string) \WebApp\Application::$ROOT_PATH;
        $docsDir = $root . DIRECTORY_SEPARATOR . 'docs';

        $filename = str_replace(['\\', '..'], ['/', ''], $filename);
        $filename = ltrim($filename, '/');

        $path = $docsDir . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path)) {
            return new Response('<h1>Not found</h1>', 404, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        $md = (string) file_get_contents($path);
        $title = htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
        $body = htmlspecialchars($md, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{$title}</title>
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;margin:24px;line-height:1.5}
      a{color:inherit}
      pre{background:#0b1020;color:#e8edf6;padding:16px;border-radius:10px;overflow:auto}
      .hint{color:#555;margin:0 0 12px}
    </style>
  </head>
  <body>
    <p class="hint">Docs are plain Markdown (rendered as preformatted text). File: <strong>{$title}</strong></p>
    <pre>{$body}</pre>
  </body>
</html>
HTML;

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}

