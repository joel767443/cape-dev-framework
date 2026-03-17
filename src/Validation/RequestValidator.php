<?php

namespace WebApp\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebApp\Http\Exception\HttpException;

final class RequestValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @param Constraint|Constraint[] $constraints
     * @return array<string, mixed>
     */
    public function validateJson(Request $request, Constraint|array $constraints): array
    {
        $data = $request->toArray();
        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) === 0) {
            return $data;
        }

        $errors = [];
        foreach ($violations as $v) {
            $path = $v->getPropertyPath() !== '' ? $v->getPropertyPath() : 'payload';
            $errors[$path][] = $v->getMessage();
        }

        throw new HttpException(422, 'Validation failed', ['errors' => $errors]);
    }
}

