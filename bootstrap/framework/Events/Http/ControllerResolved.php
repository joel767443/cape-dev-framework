<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Events\Http;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
final class ControllerResolved
{
    /**
     * @param Request $request
     * @param mixed $controller
     */
    public function __construct(
        public readonly Request $request,
        public readonly mixed $controller
    ) {
    }
}

