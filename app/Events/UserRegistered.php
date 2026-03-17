<?php

namespace App\Events;

use App\Models\User;

final class UserRegistered
{
    public function __construct(public readonly User $user)
    {
    }
}

