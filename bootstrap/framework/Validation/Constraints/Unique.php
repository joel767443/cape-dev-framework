<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Validation\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 *
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Unique extends Constraint
{
    public string $message = 'This value is already taken.';

    /**
     * @param string $table
     * @param string $column
     * @param int|null $ignoreId
     * @param string $idColumn
     * @param string|null $message
     * @param mixed|null $options
     * @param array|null $groups
     * @param mixed|null $payload
     */
    public function __construct(
        public readonly string $table,
        public readonly string $column,
        public readonly ?int $ignoreId = null,
        public readonly string $idColumn = 'id',
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

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return UniqueValidator::class;
    }
}

