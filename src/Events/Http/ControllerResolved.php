<?php

namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;

final class ControllerResolved
{
    public function __construct(
        public readonly Request $request,
        public readonly mixed $controller
    ) {
    }
}

