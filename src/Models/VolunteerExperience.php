<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class VolunteerExperience extends Model
{
    protected static string $table = 'volunteer_experiences';

    public $profile_id;
    public $organization;
    public $role;
    public $description;
    public $start_date;
    public $end_date;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'organization' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

