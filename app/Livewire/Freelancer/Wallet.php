<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Wallet extends Component
{
    public $saldo_disponivel = 0;
    public $saldo_pendente = 0;
    public $saque_minimo = 20000;
    public $taxa_saque = 20.0;
    public $valor_saque = 0;
    public $mensagem = '';

    public function mount()
    {
        $user = Auth::user();
        $this->saldo_disponivel = $user->wallet->saldo ?? 0;
        $this->saldo_pendente = $user->wallet->saldo_pendente ?? 0;
        $this->saque_minimo = $user->wallet->saque_minimo ?? 20000;
        $this->taxa_saque = $user->wallet->taxa_saque ?? 20.0;
    }

    public function solicitarSaque()
    {
        $this->validate([
            'valor_saque' => 'required|numeric|min:' . $this->saque_minimo,
        ]);
        $valor_liquido = $this->valor_saque - ($this->valor_saque * $this->taxa_saque / 100);
        $this->mensagem = "Saque solicitado: {$this->valor_saque} Kz. Valor líquido após taxa: {$valor_liquido} Kz.";
        // Aqui seria feita a lógica de solicitação de saque
    }

    public function render()
    {
        return view('livewire.freelancer.wallet');
    }
}
