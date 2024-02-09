<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ListUser extends Component
{
    use AuthorizesRequests;

    public function render()
    {
        $this->authorize('create', auth()->user() );
        return view('livewire.list-user');
    }
}
