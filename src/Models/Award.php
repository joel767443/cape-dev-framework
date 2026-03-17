<?php

namespace WebApp\Models;

class Award extends Model
{
    protected static string $table = 'awards';

    public $profile_id;
    public $title;
    public $issuer;
    public $description;
    public $award_date;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'title' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

