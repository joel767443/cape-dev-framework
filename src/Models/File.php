<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

class File extends Model
{
    protected static string $table = 'files';

    public $filename;
    public $status;
    public $uploaded_at;
    public $profile_id;

    public function rules(): array
    {
        return [
            'filename' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'status' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'uploaded_at' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
        ];
    }
}

