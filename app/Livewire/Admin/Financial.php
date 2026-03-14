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

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

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

        $totalEntradas  = WalletLog::where('created_at', '>=', $start)->where('tipo', 'escrow_retido')->sum('valor');
        $totalSaidas    = WalletLog::where('created_at', '>=', $start)->where('tipo', 'saque_aprovado')->sum('valor');
        $totalComissoes = WalletLog::where('created_at', '>=', $start)->where('tipo', 'pagamento_projeto')->sum('valor') * 10 / 90;

        return view('livewire.admin.financial', compact('logs', 'totalEntradas', 'totalSaidas', 'totalComissoes'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão Financeira']);
    }
}
