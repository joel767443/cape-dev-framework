<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class ProfileSkill extends Model
{
    protected static string $table = 'profile_skills';

    public $profile_id;
    public $skill_id;
    public $is_top_skill;
    public $source;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'skill_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'is_top_skill' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
        ];
    }
}

