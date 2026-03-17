<?php

namespace WebApp\Models;

class Profile extends Model
{
    protected static string $table = 'profiles';

    public $email;
    public $name;
    public $phone;
    public $location;
    public $headline;
    public $github_token;
    public $created_at;
    public $updated_at;

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

