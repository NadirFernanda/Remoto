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
        // Mock: Substitua por busca real no banco
        $this->balance = 1500.75;
        $this->recentPayments = [
            [
                'amount' => 500.00,
                'date' => now()->subDays(2)->format('d/m/Y'),
                'description' => 'Pagamento recebido de projeto X',
            ],
            [
                'amount' => -200.00,
                'date' => now()->subDays(5)->format('d/m/Y'),
                'description' => 'Pagamento realizado para serviço Y',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.client.finance-panel');
    }
}
