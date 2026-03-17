<?php

namespace WebApp\Models;

class Skill extends Model
{
    protected static string $table = 'skills';

    public $name;
    public $created_at;

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

