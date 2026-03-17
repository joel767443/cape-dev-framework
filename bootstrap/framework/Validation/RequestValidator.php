<?php

namespace WebApp\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebApp\Http\Exception\HttpException;

/**
 *
 */
final class RequestValidator
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @param Constraint|Constraint[] $constraints
     * @return array<string, mixed>
     */
    public function validateJson(Request $request, Constraint|array $constraints): array
    {
        return $this->validateArray($request->toArray(), $constraints);
    }

    /**
     * @param array<string, mixed> $data
     * @param Constraint|Constraint[] $constraints
     * @return array<string, mixed>
     */
    public function validateArray(array $data, Constraint|array $constraints): array
    {
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

