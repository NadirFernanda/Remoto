<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\WalletLog;
use App\Models\InfoprodutoCompra;
use App\Models\CreatorSubscription;
use Carbon\Carbon;

class CashFlow extends Component
{
    public string $period     = 'month';
    public string $dateStart  = '';
    public string $dateEnd    = '';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatedPeriod(): void
    {
        $this->dateStart = '';
        $this->dateEnd   = '';
    }

    private function startDate(): Carbon
    {
        if ($this->dateStart) {
            return Carbon::parse($this->dateStart)->startOfDay();
        }

        return match ($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function endDate(): Carbon
    {
        return $this->dateEnd
            ? Carbon::parse($this->dateEnd)->endOfDay()
            : Carbon::now()->endOfDay();
    }

    public function render()
    {
        $start = $this->startDate();
        $end   = $this->endDate();

        // ── Freelancing ──────────────────────────────────────────────────────
        $flEntradas  = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'escrow_retido')->sum('valor');
        $flSaidas    = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'saque_aprovado')->sum('valor');
        $flComissao  = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'pagamento_projeto')->sum('valor') * 10 / 90;

        // ── Creator ──────────────────────────────────────────────────────────
        $crEntradas  = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('amount');
        $crComissao  = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('platform_fee');
        $crSaidas    = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('net_amount');

        // ── Infoprodutos ─────────────────────────────────────────────────────
        $ipEntradas  = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_pago');
        $ipComissao  = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('comissao_plataforma');
        $ipSaidas    = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_freelancer');

        // ── Afiliados ────────────────────────────────────────────────────────
        $afEntradas  = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '>', 0)->sum('valor');
        $afSaidas    = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '<', 0)->sum('valor');

        // ── Totais ───────────────────────────────────────────────────────────
        $totalEntradas = $flEntradas + $crEntradas + $ipEntradas + $afEntradas;
        $totalSaidas   = $flSaidas   + $crSaidas   + $ipSaidas   + abs($afSaidas);
        $totalComissao = $flComissao + $crComissao + $ipComissao;
        $saldoLiquido  = $totalEntradas - $totalSaidas;

        $grupos = [
            [
                'origem'   => 'Freelancing',
                'cor'      => 'blue',
                'entradas' => $flEntradas,
                'saidas'   => $flSaidas,
                'comissao' => $flComissao,
            ],
            [
                'origem'   => 'Criador',
                'cor'      => 'purple',
                'entradas' => $crEntradas,
                'saidas'   => $crSaidas,
                'comissao' => $crComissao,
            ],
            [
                'origem'   => 'Infoprodutos',
                'cor'      => 'orange',
                'entradas' => $ipEntradas,
                'saidas'   => $ipSaidas,
                'comissao' => $ipComissao,
            ],
            [
                'origem'   => 'Afiliados',
                'cor'      => 'green',
                'entradas' => $afEntradas,
                'saidas'   => abs($afSaidas),
                'comissao' => 0,
            ],
        ];

        return view('livewire.admin.cash-flow', compact(
            'grupos',
            'totalEntradas',
            'totalSaidas',
            'totalComissao',
            'saldoLiquido',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Fluxo de Caixa']);
    }
}
