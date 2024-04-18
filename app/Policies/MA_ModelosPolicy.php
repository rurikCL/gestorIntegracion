<?php

namespace App\Policies;

use App\Models\User;

class MA_ModelosPolicy
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
        return $user->isRole(['admin', 'marketing']);

    }

    public function create(User $user)
    {
        //
        return $user->isRole(['admin', 'marketing']);

    }
    public function update(User $user)
    {
        //
        return $user->isRole(['admin', 'marketing']);

    }
}
