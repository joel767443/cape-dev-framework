<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 *
 */
abstract class Model extends EloquentModel
{
    /**
     * Keep timestamps enabled by default (Laravel/Eloquent convention).
     */
    public $timestamps = true;

    /**
     * Default guarded behavior: allow mass assignment unless app opts out.
     */
    protected $guarded = [];
}

