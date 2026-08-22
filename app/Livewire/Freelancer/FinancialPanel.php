<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\Wallet as WalletModel;
use App\Models\Service;
use App\Services\SubscriptionWithdrawalGate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialPanel extends Component
{
    public string $period = 'month';

    // Withdrawal form
    public float|int $valorSaque    = 0;
    public string    $successMsg    = '';

    public function mount(): void
    {
    }

    public function solicitarSaque(): void
    {
        $this->successMsg = '';

        $user = Auth::user();

        $minAmount  = (float) \App\Models\PlatformSetting::get('withdrawal_min_amount', 1000);
        $feeFixed   = (float) \App\Models\PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) \App\Models\PlatformSetting::get('withdraw_fee_percent', 0);

        $gatedPorAssinaturas = SubscriptionWithdrawalGate::saldoAtribuivel($user->id) > 0;

        if ($gatedPorAssinaturas) {
            $minAmount = SubscriptionWithdrawalGate::SAQUE_MINIMO;

            $diasRestantes = SubscriptionWithdrawalGate::diasParaProximoSaque($user->id);
            if ($diasRestantes > 0) {
                $this->addError('valorSaque', "Tem saldo de assinaturas por resgatar — só pode voltar a sacar daqui a {$diasRestantes} dia(s).");
                return;
            }
        }

        $this->validate([
            'valorSaque' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valorSaque.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        $fee               = round($feeFixed + ($this->valorSaque * $feePercent / 100), 2);
        $valorLiquidoSaque = round($this->valorSaque - $fee, 2);

        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
        );

        if ($wallet->saldo < $this->valorSaque) {
            $this->addError('valorSaque', 'Saldo insuficiente para este saque.');
            return;
        }

        // Verificar se já existe um saque pendente (evita múltiplos saques simultâneos)
        $pendingSaque = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->exists();

        if ($pendingSaque) {
            $this->addError('valorSaque', 'Já tem um saque pendente de aprovação. Aguarde a resolução antes de solicitar outro.');
            return;
        }

        // Mover valor do saldo disponível para saldo_pendente (em análise)
        // Uso de transacção para garantir atomicidade: se WalletLog falhar,
        // os decrements/increments são revertidos automaticamente.
        \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $user, $fee, $valorLiquidoSaque, $gatedPorAssinaturas) {
            $wallet->decrement('saldo', $this->valorSaque);
            $wallet->increment('saldo_pendente', $this->valorSaque);

            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valorSaque,
                'tipo'      => 'saque_solicitado',
                'fonte'     => $gatedPorAssinaturas ? 'assinaturas' : null,
                'descricao' => "Saque solicitado de " . number_format($this->valorSaque, 2, ',', '.') . " Kz — taxa " . number_format($fee, 2, ',', '.') . " Kz — valor líquido a receber: " . number_format($valorLiquidoSaque, 2, ',', '.') . " Kz — a aguardar aprovação do admin.",
            ]);
        });

        $this->successMsg = "Saque de Kz " . number_format($this->valorSaque, 0, ',', '.') . " solicitado com sucesso. Taxa: Kz " . number_format($fee, 2, ',', '.') . ". Receberá: Kz " . number_format($valorLiquidoSaque, 2, ',', '.') . ". Será processado em até 2 dias úteis.";
        $this->valorSaque = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        $user   = auth()->user();
        $wallet = $user->wallet;

        $baseQuery = WalletLog::where('user_id', $user->id);
        $now       = Carbon::now();

        $filteredQuery = match ($this->period) {
            'month'      => (clone $baseQuery)->whereBetween('created_at', [
                                $now->copy()->startOfMonth(),
                                $now->copy()->endOfMonth(),
                            ]),
            'last_month' => (clone $baseQuery)->whereBetween('created_at', [
                                $now->copy()->subMonth()->startOfMonth(),
                                $now->copy()->subMonth()->endOfMonth(),
                            ]),
            'quarter'    => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subMonths(3)),
            default      => (clone $baseQuery),
        };

        $logs = $filteredQuery->orderByDesc('created_at')->get();

        $ganhos     = $logs->where('tipo', 'ganho')->sum('valor');
        $taxas      = $logs->where('tipo', 'taxa')->sum('valor');
        $saques     = $logs->where('tipo', 'saque')->sum('valor');
        $reembolsos = $logs->where('tipo', 'reembolso')->sum('valor');

        // Services with pending payment
        $pendingServices = Service::where('freelancer_id', $user->id)
            ->whereIn('status', ['accepted', 'in_progress', 'delivered'])
            ->get();

        $saldoAssinAtribuivel      = SubscriptionWithdrawalGate::saldoAtribuivel($user->id);
        $gatedPorAssinaturas       = $saldoAssinAtribuivel > 0;
        $diasParaProximoSaqueAssin = $gatedPorAssinaturas ? SubscriptionWithdrawalGate::diasParaProximoSaque($user->id) : 0;

        return view('livewire.freelancer.financial-panel', [
            'wallet'                    => $wallet,
            'logs'                      => $logs,
            'ganhos'                    => $ganhos,
            'taxas'                     => $taxas,
            'saques'                    => $saques,
            'reembolsos'                => $reembolsos,
            'pendingServices'           => $pendingServices,
            'gatedPorAssinaturas'       => $gatedPorAssinaturas,
            'saldoAssinAtribuivel'      => $saldoAssinAtribuivel,
            'diasParaProximoSaqueAssin' => $diasParaProximoSaqueAssin,
            'saqueMinimoAssinaturas'    => SubscriptionWithdrawalGate::SAQUE_MINIMO,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
