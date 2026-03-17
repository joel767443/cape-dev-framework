<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebApp\Application;

/**
 *
 */
final class HttpKernelTest extends TestCase
{
    /**
     * @return void
     */
    public function testJwtProtectedPingReturnsJson(): void
    {
        require_once __DIR__ . '/../autoload.php';

        putenv('JWT_SECRET=0123456789abcdef0123456789abcdef');

        $app = new Application(dirname(__DIR__));
        $router = $app->router;
        require __DIR__ . '/../routes/api.php';

        $tokenReq = Request::create('/api/auth/token', 'POST', [], [], [], [], json_encode(['sub' => 'demo']));
        $tokenReq->headers->set('Content-Type', 'application/json');
        $tokenRes = $app->handle($tokenReq);
        self::assertSame(200, $tokenRes->getStatusCode());
        $tokenPayload = json_decode((string) $tokenRes->getContent(), true);
        $token = (string) ($tokenPayload['data']['token'] ?? '');
        self::assertNotSame('', $token);

        $request = Request::create('/api/secure/ping', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);
        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'));
    }
}

