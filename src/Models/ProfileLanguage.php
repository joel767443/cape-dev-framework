<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class ProfileLanguage extends Model
{
    protected static string $table = 'profile_languages';

    public $profile_id;
    public $language_name;
    public $proficiency;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'language_name' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

