<?php

namespace App\Policies;

use App\Models\SIS\SIS_StockFull;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MA_CargosPerfilPolicy
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
        return $user->isAdmin();
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
