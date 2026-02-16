<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FinancePanel extends Component
{
    public $balance = 0;
    public $recentPayments = [];

    public function mount()
    {
        $user = Auth::user();
        // Balance e pagamentos reais usando Wallet / WalletLog quando disponível
        $this->balance = optional($user->wallet)->saldo ?? 0;
        $logs = \App\Models\WalletLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();
        $this->recentPayments = $logs->map(function($l) {
            return [
                'amount' => $l->valor,
                'created_at' => $l->created_at,
                'description' => $l->descricao ?? '',
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.client.finance-panel');
    }
}
