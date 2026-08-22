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

        // Número de ordem sequencial por cliente (1º projecto criado = #1),
        // em vez do ID da base de dados — a mesma colecção mantém a
        // ordenação de exibição (mais recente primeiro).
        foreach ($this->orders->sortBy('id')->values() as $index => $order) {
            $order->order_number = $index + 1;
        }
    }

    public function render()
    {
        return view('livewire.client.order-history')
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
