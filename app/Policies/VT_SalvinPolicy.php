<?php

namespace App\Policies;

use App\Models\User;

class VT_SalvinPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        //
        return $user->isRole(['admin', 'salvin']);
    }

    public function create(User $user)
    {
        //
        return $user->isAdmin();

    }
    public function update(User $user)
    {
        //
        return $user->isAdmin();

    }
}
