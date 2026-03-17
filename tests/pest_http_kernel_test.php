<?php

use Symfony\Component\HttpFoundation\Request;
use WebApp\Application;

it('returns JSON for /api/secure/ping when authorized', function (): void {
    require_once __DIR__ . '/../autoload.php';

    putenv('JWT_SECRET=0123456789abcdef0123456789abcdef');

    $app = new Application(dirname(__DIR__));
    $router = $app->router;
    require __DIR__ . '/../routes/api.php';

    // get a JWT
    $tokenReq = Request::create('/api/auth/token', 'POST', [], [], [], [], json_encode(['sub' => 'demo']));
    $tokenReq->headers->set('Content-Type', 'application/json');
    $tokenRes = $app->handle($tokenReq);
    expect($tokenRes->getStatusCode())->toBe(200);
    $tokenPayload = json_decode((string) $tokenRes->getContent(), true);
    $token = $tokenPayload['data']['token'] ?? '';
    expect($token)->not->toBe('');

    $req = Request::create('/api/secure/ping', 'GET');
    $req->headers->set('Authorization', 'Bearer ' . $token);
    $res = $app->handle($req);

    expect($res->getStatusCode())->toBe(200);
    expect((string) $res->headers->get('Content-Type'))->toContain('application/json');
});

