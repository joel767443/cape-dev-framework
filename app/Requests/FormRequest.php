<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use WebApp\Validation\RequestValidator;

abstract class FormRequest
{
    private ?Request $request = null;

    /**
     * @var array<string, mixed>
     */
    private array $validated = [];

    abstract public function constraints(): Constraint|array;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function request(): Request
    {
        if (!$this->request) {
            throw new \RuntimeException('FormRequest request is not set.');
        }
        return $this->request;
    }

    /**
     * Validate current request and store validated data.
     */
    public function validateResolved(RequestValidator $validator): void
    {
        $data = $this->request()->toArray();
        $this->validated = $validator->validateArray($data, $this->constraints());
    }

    /**
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->validated;
    }
}

