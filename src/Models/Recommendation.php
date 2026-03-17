<?php

namespace WebApp\Models;

class Recommendation extends Model
{
    protected static string $table = 'recommendations';

    public $profile_id;
    public $from_name;
    public $relationship;
    public $text;
    public $created_at;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'from_name' => [self::RULE_REQUIRED, self::IS_STRING],
            'text' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

