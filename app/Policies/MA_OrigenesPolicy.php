<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MA_OrigenesPolicy
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
