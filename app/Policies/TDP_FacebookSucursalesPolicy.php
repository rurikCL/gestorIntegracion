<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TDP_FacebookSucursalesPolicy
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
