<?php

namespace WebApp\Models;

class Publication extends Model
{
    protected static string $table = 'publications';

    public $profile_id;
    public $title;
    public $publisher;
    public $publication_date;
    public $url;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'title' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

