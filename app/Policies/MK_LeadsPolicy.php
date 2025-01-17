<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MK_LeadsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user)
    {
        //
        return $user->isAdmin() || $user->isMarketing();
    }

    public function create(User $user)
    {
        //
        return $user->isAdmin() || $user->isMarketing();
    }
    public function update(User $user)
    {
        //
        return $user->isAdmin() || $user->isMarketing();

    }
}
