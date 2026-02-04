<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;

class Profile extends Component
{
    use UserSessionTrait;
        // Removed extra opening curly brace
    public $user;

    public function mount()
    {
        $this->user = $this->getCurrentUser();
    }

    public function render()
    {
        return view('livewire.client.profile');
    }
}
