<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Recommendation extends Model
{
    protected static string $table = 'recommendations';

    public $profile_id;
    public $from_name;
    public $relationship;
    public $text;
    public $created_at;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'from_name' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'text' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

