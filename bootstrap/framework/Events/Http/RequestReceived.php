<?php

namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
final class RequestReceived
{
    /**
     * @param Request $request
     */
    public function __construct(public readonly Request $request)
    {
    }
}

