<?php

namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;

final class RequestReceived
{
    public function __construct(public readonly Request $request)
    {
    }
}

