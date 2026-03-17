<?php

namespace WebApp\Models;

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
            'profile_id' => [self::RULE_REQUIRED, self::IS_INT],
            'skill_id' => [self::RULE_REQUIRED, self::IS_INT],
            'is_top_skill' => [self::RULE_REQUIRED, self::IS_INT],
        ];
    }
}

