<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait UsesUuid
{
    protected static function bootUsesUuid(): void
    {
        static::creating(function (Model $model): void {
            $keyName = $model->getKeyName();
            if (!$model->getAttribute($keyName)) {
                $model->setAttribute($keyName, uuid());
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}

