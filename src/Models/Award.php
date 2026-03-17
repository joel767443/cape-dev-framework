<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Award extends Model
{
    protected static string $table = 'awards';

    public $profile_id;
    public $title;
    public $issuer;
    public $description;
    public $award_date;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'title' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

