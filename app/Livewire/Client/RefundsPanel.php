<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Refund;

class RefundsPanel extends Component
{
    use WithPagination;
    public function render()
    {
        $refunds = Refund::where('user_id', Auth::id())
            ->with('service')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('livewire.client.refunds-panel', [
            'refunds' => $refunds,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Reembolsos']);
    }
}
