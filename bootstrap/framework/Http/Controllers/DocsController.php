<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Http\Controllers;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Application;

/**
 *
 */
final class DocsController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->renderDoc('README.md');
    }

    public function queueMd(): Response
    {
        return $this->renderDoc('queue.md');
    }

    /**
     * @param Request $request
     * @return Response
     */
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
        $root = Application::$ROOT_PATH;
        $docsDir = $root . DIRECTORY_SEPARATOR . 'docs';

        $filename = str_replace(['\\', '..'], ['/', ''], $filename);
        $filename = ltrim($filename, '/');

        $path = $docsDir . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path)) {
            return new Response('<h1>Not found</h1>', 404, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        $md = (string) file_get_contents($path);
        $env = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $env->addExtension(new CommonMarkCoreExtension());
        $converter = new CommonMarkConverter([], $env);

        $rendered = $converter->convert($md);
        $html = (string) $rendered;

        $title = $this->prettyTitle($filename);

        $nav = [
            ['label' => 'Installation + quickstart', 'href' => '/docs/installation-and-quickstart', 'file' => 'installation-and-quickstart.md'],
            ['label' => 'Routing + middleware', 'href' => '/docs/routing-and-middleware', 'file' => 'routing-and-middleware.md'],
            ['label' => 'Controllers / requests / responses', 'href' => '/docs/controllers-requests-responses', 'file' => 'controllers-requests-responses.md'],
            ['label' => 'Validation', 'href' => '/docs/validation', 'file' => 'validation.md'],
            ['label' => 'Database + migrations', 'href' => '/docs/database-and-migrations', 'file' => 'database-and-migrations.md'],
            ['label' => 'Authentication (JWT)', 'href' => '/docs/auth', 'file' => 'auth.md'],
            ['label' => 'Queue', 'href' => '/docs/queue', 'file' => 'queue.md'],
            ['label' => 'How to', 'href' => '/docs/how-to', 'file' => 'how-to.md'],
        ];

        $page = view('docs.show', [
            'title' => $title,
            'active' => $filename,
            'html' => $html,
            'nav' => $nav,
        ]);

        return new Response($page, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function prettyTitle(string $filename): string
    {
        $name = preg_replace('/\\.md$/i', '', $filename) ?? $filename;
        $name = str_replace(['_', '-'], ' ', $name);
        $name = trim($name);
        if ($name === '' || strtolower($name) === 'readme') {
            return 'Docs';
        }

        return ucwords($name);
    }
}

