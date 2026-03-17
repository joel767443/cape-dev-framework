<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class Certification extends Model
{
    protected static string $table = 'certifications';

    public $profile_id;
    public $authority_name;
    public $name;
    public $license;
    public $issued_date;
    public $expiry_date;

    public function rules(): array
    {
        return [
            'profile_id' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
            'name' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

