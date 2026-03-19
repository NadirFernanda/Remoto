<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\Wallet as WalletModel;
use App\Models\Service;
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

        $this->validate([
            'valorSaque' => ['required', 'numeric', 'min:1'],
        ], [
            'valorSaque.min' => 'O valor mínimo de saque é Kz 1.',
        ]);

        $user   = Auth::user();
        $wallet = WalletModel::where('user_id', $user->id)->firstOrFail();

        if ($wallet->saldo < $this->valorSaque) {
            $this->addError('valorSaque', 'Saldo insuficiente para este saque.');
            return;
        }

        // Saque sem taxa — comissões já são cobradas no momento de cada transação
        $wallet->decrement('saldo', $this->valorSaque);

        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => -$this->valorSaque,
            'tipo'      => 'saque_solicitado',
            'descricao' => "Saque solicitado de " . number_format($this->valorSaque, 2, ',', '.') . " Kz.",
        ]);

        $this->successMsg = "Saque de Kz " . number_format($this->valorSaque, 0, ',', '.') . " solicitado com sucesso.";
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

        return view('livewire.freelancer.financial-panel', [
            'wallet'          => $wallet,
            'logs'            => $logs,
            'ganhos'          => $ganhos,
            'taxas'           => $taxas,
            'saques'          => $saques,
            'reembolsos'      => $reembolsos,
            'pendingServices' => $pendingServices,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Painel Financeiro']);
    }
}
