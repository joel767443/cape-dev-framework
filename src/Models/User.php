<?php

namespace WebApp\Models;

class User extends Model
{
    protected static string $table = 'users';

    public $email;
    public $name;
    public $created_at;
    public $updated_at;

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

