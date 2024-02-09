<?php

namespace App\Policies;

use App\Models\SIS\SIS_StockFull;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VT_SalvinPolicy
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
    public function view(User $user)
    {
        //
        return $user->isRole(['salvin', 'admin']);
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
