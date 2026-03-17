<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

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
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
        ];
    }
}

