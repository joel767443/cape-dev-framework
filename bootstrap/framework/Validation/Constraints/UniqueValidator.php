<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Validation\Constraints;

use Illuminate\Database\ConnectionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 *
 */
final class UniqueValidator extends ConstraintValidator
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
        if (!$constraint instanceof Unique) {
            throw new UnexpectedTypeException($constraint, Unique::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $q = $this->db
            ->table($constraint->table)
            ->where($constraint->column, '=', $value);

        if ($constraint->ignoreId !== null) {
            $q->where($constraint->idColumn, '<>', $constraint->ignoreId);
        }

        if ($q->exists()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

