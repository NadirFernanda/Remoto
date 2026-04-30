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
        $minAmount = (float) \App\Models\PlatformSetting::get('withdrawal_min_amount', 1000);
        $feeFixed   = (float) \App\Models\PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) \App\Models\PlatformSetting::get('withdraw_fee_percent', 0);

        $this->validate([
            'valor_saque' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valor_saque.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        $fee        = round($feeFixed + ($this->valor_saque * $feePercent / 100), 2);
        $valorLiquidoSaque = round($this->valor_saque - $fee, 2);

        $user   = Auth::user();
        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
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

        // Mover para saldo_pendente dentro de transacção atómica
        \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $user) {
            $wallet->decrement('saldo', $this->valor_saque);
            $wallet->increment('saldo_pendente', $this->valor_saque);

            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valor_saque,
                'tipo'      => 'saque_solicitado',
                'descricao' => "Saque solicitado de " . number_format($this->valor_saque, 0, ',', '.') . " Kz — taxa " . number_format($fee, 2, ',', '.') . " Kz — valor líquido a receber: " . number_format($valorLiquidoSaque, 2, ',', '.') . " Kz — a aguardar aprovação do admin.",
            ]);
        });

        $this->saldo_disponivel = $wallet->fresh()->saldo;
        $this->saldo_pendente   = $wallet->fresh()->saldo_pendente;
        $this->mensagem = "Saque de " . number_format($this->valor_saque, 0, ',', '.') . " Kz solicitado. Taxa: " . number_format($fee, 2, ',', '.') . " Kz. Receberá: " . number_format($valorLiquidoSaque, 2, ',', '.') . " Kz em até 2 dias úteis.";
        $this->valor_saque = 0;
        session()->flash('success', $this->mensagem);
    }

    public function render()
    {
        return view('livewire.freelancer.wallet')
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
