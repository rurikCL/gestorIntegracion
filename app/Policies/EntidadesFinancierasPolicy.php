<?php

namespace App\Policies;

use App\Models\EntidadesFinancieras;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntidadesFinancierasPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        //
        return false;
    }

    public function view(User $user, EntidadesFinancieras $model)
    {
        //
        return false;

    }
}
