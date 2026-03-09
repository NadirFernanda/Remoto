<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet as WalletModel;
use App\Models\WalletLog;

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

        $user   = Auth::user();
        $wallet = WalletModel::where('user_id', $user->id)->firstOrFail();

        if ($wallet->saldo < $this->valor_saque) {
            $this->addError('valor_saque', 'Saldo insuficiente.');
            return;
        }

        $valor_liquido = round($this->valor_saque - ($this->valor_saque * $this->taxa_saque / 100), 2);

        // Debitar saldo
        $wallet->decrement('saldo', $this->valor_saque);

        // Registar log
        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => -$this->valor_saque,
            'tipo'      => 'saque_solicitado',
            'descricao' => "Saque solicitado de {$this->valor_saque} Kz. Líquido após taxa: {$valor_liquido} Kz.",
        ]);

        $this->saldo_disponivel = $wallet->fresh()->saldo;
        $this->mensagem = "Saque de " . number_format($this->valor_saque, 0, ',', '.') . " Kz solicitado. Receberá " . number_format($valor_liquido, 0, ',', '.') . " Kz após taxa de {$this->taxa_saque}%.";
        $this->valor_saque = 0;
        session()->flash('success', $this->mensagem);
    }

    public function render()
    {
        return view('livewire.freelancer.wallet');
    }
}
