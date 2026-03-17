<?php

namespace WebApp\Models;

class Project extends Model
{
    protected static string $table = 'projects';

    public $profile_id;
    public $name;
    public $description;
    public $url;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'name' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

