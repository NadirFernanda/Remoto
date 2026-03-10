<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Refund;

class RefundsPanel extends Component
{
    public function render()
    {
        $refunds = Refund::where('user_id', Auth::id())
            ->with('service')
            ->orderByDesc('created_at')
            ->get();
        return view('livewire.client.refunds-panel', [
            'refunds' => $refunds,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Reembolsos']);
    }
}
