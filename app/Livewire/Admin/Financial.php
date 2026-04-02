<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\InfoprodutoCompra;
use App\Models\CreatorSubscription;
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

        // ── Receita por modelo de negócio ──────────────────────────────────
        $receitaFreelancing  = (float) WalletLog::where('created_at', '>=', $start)
            ->where('tipo', 'pagamento_projeto')
            ->sum('valor') * 10 / 90;

        $receitaCreator = (float) CreatorSubscription::where('created_at', '>=', $start)
            ->sum('platform_fee');

        $receitaInfoprodutos = (float) InfoprodutoCompra::where('created_at', '>=', $start)
            ->sum('comissao_plataforma');

        $receitaTotal = $receitaFreelancing + $receitaCreator + $receitaInfoprodutos;

        // ── Retenção (Escrow / Pagamento em garantia) ──────────────────────
        $escrowRetidoTotal   = (float) WalletLog::where('tipo', 'escrow_retido')->sum('valor');
        $escrowLiberadoTotal = (float) WalletLog::where('tipo', 'escrow_liberado')->sum('valor');
        $escrowEmRetencao    = max(0, $escrowRetidoTotal - $escrowLiberadoTotal);   // actualmente retido

        $escrowLiberadoPeriodo = (float) WalletLog::where('created_at', '>=', $start)
            ->where('tipo', 'escrow_liberado')
            ->sum('valor');

        $escrowRetidoPeriodo = (float) WalletLog::where('created_at', '>=', $start)
            ->where('tipo', 'escrow_retido')
            ->sum('valor');

        return view('livewire.admin.financial', compact(
            'logs',
            'totalEntradas',
            'totalSaidas',
            'totalComissoes',
            'receitaFreelancing',
            'receitaCreator',
            'receitaInfoprodutos',
            'receitaTotal',
            'escrowEmRetencao',
            'escrowRetidoPeriodo',
            'escrowLiberadoPeriodo',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão Financeira']);
    }
}
