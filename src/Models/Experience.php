<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

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
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'role_title' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'is_current' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
        ];
    }
}

