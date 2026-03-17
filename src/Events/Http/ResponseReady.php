<?php

namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResponseReady
{
    public function __construct(
        public readonly Request $request,
        public readonly Response $response
    ) {
    }
}

