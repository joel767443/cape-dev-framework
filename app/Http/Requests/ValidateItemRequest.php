<?php

namespace App\Http\Requests;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class ValidateItemRequest extends FormRequest
{
    public function constraints(): Constraint|array
    {
        return new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(min: 2, max: 120),
            ],
        ]);
    }
}

