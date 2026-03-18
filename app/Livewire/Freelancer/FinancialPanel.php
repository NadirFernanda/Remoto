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
    public float|int $saqueMinimo   = 20000;
    public float     $taxaSaque     = 20.0;
    public string    $successMsg    = '';

    public function mount(): void
    {
        $wallet           = Auth::user()->wallet;
        $this->saqueMinimo = $wallet->saque_minimo ?? 20000;
        $this->taxaSaque   = $wallet->taxa_saque   ?? 20.0;
    }

    public function solicitarSaque(): void
    {
        $this->successMsg = '';

        $this->validate([
            'valorSaque' => ['required', 'numeric', 'min:' . $this->saqueMinimo],
        ], [
            'valorSaque.min' => "O valor mínimo de saque é Kz " . number_format($this->saqueMinimo, 0, ',', '.') . ".",
        ]);

        $user   = Auth::user();
        $wallet = WalletModel::where('user_id', $user->id)->firstOrFail();

        if ($wallet->saldo < $this->valorSaque) {
            $this->addError('valorSaque', 'Saldo insuficiente para este saque.');
            return;
        }

        $liquido = round($this->valorSaque - ($this->valorSaque * $this->taxaSaque / 100), 2);

        $wallet->decrement('saldo', $this->valorSaque);

        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => -$this->valorSaque,
            'tipo'      => 'saque_solicitado',
            'descricao' => "Saque solicitado de " . number_format($this->valorSaque, 2, ',', '.') . " Kz. Líquido após taxa: " . number_format($liquido, 2, ',', '.') . " Kz.",
        ]);

        $this->successMsg = "Saque de Kz " . number_format($this->valorSaque, 0, ',', '.') . " solicitado com sucesso. Receberá Kz " . number_format($liquido, 0, ',', '.') . " após taxa de {$this->taxaSaque}%.";
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
