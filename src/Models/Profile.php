<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

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
            'email' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

