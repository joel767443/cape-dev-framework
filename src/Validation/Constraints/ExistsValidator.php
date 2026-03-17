<?php

namespace WebApp\Validation\Constraints;

use Illuminate\Database\ConnectionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly ConnectionInterface $db)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Exists) {
            throw new UnexpectedTypeException($constraint, Exists::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $exists = (bool) $this->db
            ->table($constraint->table)
            ->where($constraint->column, '=', $value)
            ->exists();

        if (!$exists) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

