<?php

namespace WebApp\Models;

class ProfileLanguage extends Model
{
    protected static string $table = 'profile_languages';

    public $profile_id;
    public $language_name;
    public $proficiency;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'language_name' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

