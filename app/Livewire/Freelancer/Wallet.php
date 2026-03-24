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
    public $valor_saque = 0;
    public $mensagem = '';

    public function mount()
    {
        $user = Auth::user();
        $this->saldo_disponivel = $user->wallet->saldo ?? 0;
        $this->saldo_pendente = $user->wallet->saldo_pendente ?? 0;
    }

    public function solicitarSaque()
    {
        $this->validate([
            'valor_saque' => 'required|numeric|min:1000',
        ], [
            'valor_saque.min' => 'O valor mínimo de saque é Kz 1.000.',
        ]);

        $user   = Auth::user();
        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]
        );

        if ($wallet->saldo < $this->valor_saque) {
            $this->addError('valor_saque', 'Saldo insuficiente.');
            return;
        }

        // Prevenir múltiplos saques simultâneos
        $pendingSaque = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->exists();

        if ($pendingSaque) {
            $this->addError('valor_saque', 'Já tem um saque pendente de aprovação. Aguarde a resolução antes de solicitar outro.');
            return;
        }

        // Mover para saldo_pendente (não desaparece do saldo sem aviso)
        $wallet->decrement('saldo', $this->valor_saque);
        $wallet->increment('saldo_pendente', $this->valor_saque);

        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => -$this->valor_saque,
            'tipo'      => 'saque_solicitado',
            'descricao' => "Saque solicitado de " . number_format($this->valor_saque, 0, ',', '.') . " Kz — a aguardar aprovação do admin.",
        ]);

        $this->saldo_disponivel = $wallet->fresh()->saldo;
        $this->saldo_pendente   = $wallet->fresh()->saldo_pendente;
        $this->mensagem = "Saque de " . number_format($this->valor_saque, 0, ',', '.') . " Kz solicitado. Será processado em até 2 dias úteis.";
        $this->valor_saque = 0;
        session()->flash('success', $this->mensagem);
    }

    public function render()
    {
        return view('livewire.freelancer.wallet')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Carteira & Saques']);
    }
}
