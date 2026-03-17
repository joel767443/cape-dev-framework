<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;

final class User extends Model
{
    use UsesUuid;

    protected $table = 'users';

    protected $hidden = ['password'];

    protected $fillable = ['email', 'password'];
}

