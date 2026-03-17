<?php

namespace WebApp\Models;

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
            'filename' => [self::RULE_REQUIRED, self::IS_STRING],
            'status' => [self::RULE_REQUIRED, self::IS_STRING],
            'uploaded_at' => [self::RULE_REQUIRED, self::IS_STRING],
        ];
    }
}

