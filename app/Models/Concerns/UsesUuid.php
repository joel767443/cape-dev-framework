<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
trait UsesUuid
{
    /**
     * @return void
     */
    protected static function bootUsesUuid(): void
    {
        static::creating(function (Model $model): void {
            $keyName = $model->getKeyName();
            if (!$model->getAttribute($keyName)) {
                $model->setAttribute($keyName, uuid());
            }
        });
    }

    /**
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}

