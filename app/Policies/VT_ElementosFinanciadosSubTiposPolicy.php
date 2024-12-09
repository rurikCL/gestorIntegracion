<?php

namespace App\Policies;

use App\Models\User;

class VT_ElementosFinanciadosSubTiposPolicy
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
        return $user->isRole(['admin','analista']);
    }

    public function create(User $user)
    {
        return $user->isRole(['admin','analista']);

    }
    public function update(User $user)
    {
        return $user->isRole(['admin','analista']);

    }
    public function delete(User $user)
    {
        return $user->isRole(['admin']);

    }
}
