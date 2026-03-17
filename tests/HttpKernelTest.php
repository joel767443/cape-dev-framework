<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebApp\Application;

final class HttpKernelTest extends TestCase
{
    public function testApiItemsReturnsJson(): void
    {
        require_once __DIR__ . '/../autoload.php';

        $app = new Application(dirname(__DIR__));
        $router = $app->router;
        require __DIR__ . '/../routes/api.php';

        $request = Request::create('/api/items', 'GET');
        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'));
    }
}

