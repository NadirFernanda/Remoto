<?php

namespace App\Services;

use App\Models\CreatorSubscription;
use App\Models\InfoprodutoCompra;
use App\Models\WalletLog;
use Carbon\Carbon;

/**
 * Cálculo do fluxo de caixa da plataforma para um intervalo de datas.
 *
 * Partilhado entre o relatório em tempo real (Admin\CashFlow) e o fecho diário
 * persistido (CashFlowClosing / comando cashflow:close), para nunca divergir a
 * lógica de cálculo entre os dois.
 */
class CashFlowService
{
    public function calculate(Carbon $start, Carbon $end): array
    {
        // ── Freelancing ──────────────────────────────────────────────────────
        $flEntradas = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'escrow_retido')->sum('valor');
        $flSaidas   = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'saque_aprovado')->sum('valor');
        $flComissao = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'pagamento_projeto')->sum('valor') * 10 / 90;

        // ── Creator ──────────────────────────────────────────────────────────
        $crEntradas = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('amount');
        $crComissao = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('platform_fee');
        $crSaidas   = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('net_amount');

        // ── Infoprodutos ─────────────────────────────────────────────────────
        $ipEntradas = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_pago');
        $ipComissao = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('comissao_plataforma');
        $ipSaidas   = (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_freelancer');

        // ── Afiliados ────────────────────────────────────────────────────────
        $afEntradas = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '>', 0)->sum('valor');
        $afSaidas   = (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '<', 0)->sum('valor');

        // ── Totais ───────────────────────────────────────────────────────────
        $totalEntradas = $flEntradas + $crEntradas + $ipEntradas + $afEntradas;
        $totalSaidas   = $flSaidas   + $crSaidas   + $ipSaidas   + abs($afSaidas);
        $totalComissao = $flComissao + $crComissao + $ipComissao;
        $saldoLiquido  = $totalEntradas - $totalSaidas;

        $grupos = [
            ['origem' => 'Freelancing',   'cor' => 'blue',   'entradas' => $flEntradas, 'saidas' => $flSaidas,        'comissao' => $flComissao],
            ['origem' => 'Criador',       'cor' => 'purple', 'entradas' => $crEntradas, 'saidas' => $crSaidas,        'comissao' => $crComissao],
            ['origem' => 'Infoprodutos',  'cor' => 'orange', 'entradas' => $ipEntradas, 'saidas' => $ipSaidas,        'comissao' => $ipComissao],
            ['origem' => 'Afiliados',     'cor' => 'green',  'entradas' => $afEntradas, 'saidas' => abs($afSaidas),   'comissao' => 0],
        ];

        return [
            'grupos'         => $grupos,
            'totalEntradas'  => $totalEntradas,
            'totalSaidas'    => $totalSaidas,
            'totalComissao'  => $totalComissao,
            'saldoLiquido'   => $saldoLiquido,
        ];
    }
}
