<?php

namespace WebApp\Models;

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
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'organization' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

