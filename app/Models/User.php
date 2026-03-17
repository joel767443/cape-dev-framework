<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;

/**
 *
 */
final class User extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string[]
     */
    protected $hidden = ['password'];

    /**
     * @var string[]
     */
    protected $fillable = ['email', 'password'];
}

