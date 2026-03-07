<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\Service;
use Carbon\Carbon;

class FinancialPanel extends Component
{
    public string $period = 'month';

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
        ])->layout('layouts.main');
    }
}
