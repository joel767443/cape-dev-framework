<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class LoginRequest extends FormRequest
{
    public function constraints(): Constraint|array
    {
        return new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(max: 190),
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(min: 8, max: 200),
            ],
        ]);
    }
}

