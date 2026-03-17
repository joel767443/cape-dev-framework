<?php

namespace WebApp\Validation\Constraints;

use Illuminate\Database\ConnectionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 *
 */
final class ExistsValidator extends ConstraintValidator
{
    /**
     * @param ConnectionInterface $db
     */
    public function __construct(private readonly ConnectionInterface $db)
    {
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Exists) {
            throw new UnexpectedTypeException($constraint, Exists::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $exists = $this->db
            ->table($constraint->table)
            ->where($constraint->column, '=', $value)
            ->exists();

        if (!$exists) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

