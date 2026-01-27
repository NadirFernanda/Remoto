<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class Dashboard extends Component
{
    public $orders = [];

    public function mount()
    {
        $user = Auth::user();
        $this->orders = Service::where('cliente_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.client.dashboard');
    }
}
