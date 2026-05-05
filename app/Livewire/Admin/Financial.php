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

        // ── Alertas de Variações Anômalas ────────────────────────────────
        $alerts = [];

        // Comparar com período anterior
        $previousStart = match ($this->period) {
            'week'  => $start->copy()->subWeek(),
            'month' => $start->copy()->subMonth(),
            'year'  => $start->copy()->subYear(),
            default => $start->copy()->subMonth(),
        };
        $previousEnd = $start->copy()->subSecond();

        $previousReceitaFreelancing = (float) WalletLog::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('tipo', 'pagamento_projeto')
            ->sum('valor') * 10 / 90;
        $previousReceitaCreator = (float) CreatorSubscription::whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('platform_fee');
        $previousReceitaInfoprodutos = (float) InfoprodutoCompra::whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('comissao_plataforma');
        $previousReceitaTotal = $previousReceitaFreelancing + $previousReceitaCreator + $previousReceitaInfoprodutos;

        if ($previousReceitaTotal > 0) {
            $variation = (($receitaTotal - $previousReceitaTotal) / $previousReceitaTotal) * 100;
            if ($variation < -50) {
                $alerts[] = [
                    'type' => 'danger',
                    'message' => "Receita caiu {:.1f}% em comparação com o período anterior ({$this->period}). Verifique possíveis problemas.",
                    'variation' => $variation
                ];
            } elseif ($variation > 100) {
                $alerts[] = [
                    'type' => 'success',
                    'message' => "Receita aumentou {:.1f}% em comparação com o período anterior ({$this->period}). Excelente desempenho!",
                    'variation' => $variation
                ];
            }
        }

        // Alerta se receita abaixo de threshold (ex: 10000 Kz)
        if ($receitaTotal < 10000) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Receita total está abaixo do threshold mínimo de 10.000 Kz. Monitore de perto.'
            ];
        }

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
            'alerts',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão Financeira']);
    }
}
