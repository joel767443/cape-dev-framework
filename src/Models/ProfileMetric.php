<?php

namespace WebApp\Models;

class ProfileMetric extends Model
{
    protected static string $table = 'profile_metrics';

    public $profile_id;
    public $certifications_count;
    public $companies_worked;
    public $projects_count;
    public $recommendations_count;
    public $skills_count;

    public function rules(): array
    {
        return [
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
        ];
    }
}

