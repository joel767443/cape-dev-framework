<?php

namespace WebApp\Models;

class Certification extends Model
{
    protected static string $table = 'certifications';

    public $profile_id;
    public $authority_name;
    public $name;
    public $license;
    public $issued_date;
    public $expiry_date;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'name' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

