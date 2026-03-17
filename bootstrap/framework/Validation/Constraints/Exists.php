<?php

namespace WebApp\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Exists extends Constraint
{
    public string $message = 'This value does not exist.';

    public function __construct(
        public readonly string $table,
        public readonly string $column = 'id',
        ?string $message = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);
        if ($message !== null) {
            $this->message = $message;
        }
    }

    public function validatedBy(): string
    {
        return ExistsValidator::class;
    }
}

