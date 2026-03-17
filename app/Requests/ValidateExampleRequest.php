<?php

namespace App\Http\Requests;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class ValidateExampleRequest extends FormRequest
{
    public function constraints(): Constraint|array
    {
        return new Assert\Collection([
            'message' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(min: 2, max: 200),
            ],
        ]);
    }
}

