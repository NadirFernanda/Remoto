<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Financial extends Component
{
    public string $period = 'month';

    public function render()
    {
        $now   = Carbon::now();
        $start = match ($this->period) {
            'week'  => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            'year'  => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $logs = WalletLog::with('user')
            ->where('created_at', '>=', $start)
            ->orderByDesc('created_at')
            ->paginate(50);

        $totalEntradas  = WalletLog::where('created_at', '>=', $start)->where('tipo', 'like', '%entrada%')->sum('valor');
        $totalSaidas    = WalletLog::where('created_at', '>=', $start)->where('tipo', 'like', '%saida%')->sum('valor');
        $totalComissoes = WalletLog::where('created_at', '>=', $start)->where('tipo', 'comissao')->sum('valor');

        return view('livewire.admin.financial', compact('logs', 'totalEntradas', 'totalSaidas', 'totalComissoes'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão Financeira']);
    }
}
