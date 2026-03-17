<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace App\Events;

use App\Models\User;

/**
 *
 */
final class UserRegistered
{
    /**
     * @param User $user
     */
    public function __construct(public readonly User $user)
    {
    }
}

