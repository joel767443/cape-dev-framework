<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

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
            'email' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

