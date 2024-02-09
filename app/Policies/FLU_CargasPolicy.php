<?php

namespace App\Policies;

use App\Models\FLU\FLU_Cargas;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FLU_CargasPolicy
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
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
        return $user->isRole(['salvin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Api\FLU_Cargas  $fluCargas
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FLU_Cargas $fluCargas)
    {
        //
        return $user->isRole(['salvin', 'admin']);

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
        return $user->isRole(['salvin', 'admin']);

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FLU\FLU_Cargas  $fluCargas
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FLU_Cargas $fluCargas)
    {
        //
        return $user->isAdmin();

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Api\FLU_Cargas  $fluCargas
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FLU_Cargas $fluCargas)
    {
        //
        return $user->isAdmin();

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Api\FLU_Cargas  $fluCargas
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FLU_Cargas $fluCargas)
    {
        //
        return $user->isAdmin();

    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Api\FLU_Cargas  $fluCargas
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, FLU_Cargas $fluCargas)
    {
        //
    }
}
