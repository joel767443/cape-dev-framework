<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Education extends Model
{
    protected static string $table = 'education';

    public $profile_id;
    public $school_name_raw;
    public $degree;
    public $field_of_study;
    public $start_date;
    public $end_date;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
        ];
    }
}

