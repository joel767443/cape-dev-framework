<?php

namespace WebApp\Models;

class Experience extends Model
{
    protected static string $table = 'experiences';

    public $profile_id;
    public $company_name_raw;
    public $role_title;
    public $location;
    public $description;
    public $start_date;
    public $end_date;
    public $is_current;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'role_title' => [self::RULE_REQUIRED, self::IS_STRING],
            'is_current' => [self::RULE_REQUIRED, self::IS_INT],
        ];
    }
}

