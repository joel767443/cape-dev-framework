<?php

namespace WebApp\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
final class HomeController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $html = view('landing');

        return new Response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}

