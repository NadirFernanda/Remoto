<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class OrderHistory extends Component
{
    public $orders = [];

    public function mount()
    {
        $user = Auth::user();
        $this->orders = Service::where('cliente_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.client.order-history')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Meus Pedidos']);
    }
}
