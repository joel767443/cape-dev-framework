<?php

namespace WebApp\Http\Controllers;

use App\Http\Requests\ValidateItemRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Temporary controller to keep existing routes working.
 * Replace with real controllers/services once DI is introduced.
 */
final class ItemsController
{
    public function index(Request $request): Response
    {
        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [],
        ], 200);
    }

    public function show(Request $request): Response
    {
        return new JsonResponse([
            'success' => false,
            'code' => 501,
            'message' => 'Not Implemented',
            'data' => [],
        ], 501);
    }

    public function create(Request $request): Response
    {
        return new JsonResponse([
            'success' => false,
            'code' => 501,
            'message' => 'Not Implemented',
            'data' => [],
        ], 501);
    }

    public function delete(Request $request): Response
    {
        return new JsonResponse([
            'success' => false,
            'code' => 501,
            'message' => 'Not Implemented',
            'data' => [],
        ], 501);
    }

    public function update(Request $request): Response
    {
        return new JsonResponse([
            'success' => false,
            'code' => 501,
            'message' => 'Not Implemented',
            'data' => [],
        ], 501);
    }

    public function validateExample(ValidateItemRequest $request): Response
    {
        return new JsonResponse([
            'success' => true,
            'code' => 200,
            'message' => 'Validated',
            'data' => $request->validated(),
        ], 200);
    }
}

