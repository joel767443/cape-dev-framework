<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Skill extends Model
{
    protected static string $table = 'skills';

    public $name;
    public $created_at;

    public function rules(): array
    {
        return [
            'name' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

