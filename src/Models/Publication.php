<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Publication extends Model
{
    protected static string $table = 'publications';

    public $profile_id;
    public $title;
    public $publisher;
    public $publication_date;
    public $url;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'title' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

