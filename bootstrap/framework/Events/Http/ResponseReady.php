<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
final class ResponseReady
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(
        public readonly Request $request,
        public readonly Response $response
    ) {
    }
}

