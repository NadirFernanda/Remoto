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
        // "Saídas" tem de reflectir dinheiro que realmente saiu da plataforma —
        // um saque aprovado — nunca o valor apenas creditado/acumulado na
        // carteira do prestador. 'fonte' distingue saques de saldo vindo de
        // assinaturas (ver SubscriptionWithdrawalGate) dos restantes, por isso
        // é o que separa esta fatia da fatia "Criador" abaixo — ambas saem do
        // mesmo saque_aprovado, nunca de um saque em separado por origem.
        // escrow_retido e saque_aprovado ficam sempre gravados com valor
        // negativo (débito), mesmo depois de aprovados — sem abs() aqui os
        // totais abaixo ficavam subtraídos em vez de somados.
        // taxa_cliente_plataforma: sobretaxa de 10% cobrada ao cliente por
        // cima do valor do projecto (ver FeeService::calculateServiceFee) —
        // dinheiro que entra e é comissão da plataforma desde logo, ao
        // contrário da comissão do lado do freelancer (só reconhecida no
        // pagamento_projeto, à saída).
        $flEntradas = abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'escrow_retido')->sum('valor'))
            + abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'taxa_cliente_plataforma')->sum('valor'));
        $flSaidas   = abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'saque_aprovado')
            ->where(fn ($q) => $q->whereNull('fonte')->orWhere('fonte', '!=', 'assinaturas'))
            ->sum('valor'));
        $flComissao = ((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'pagamento_projeto')->sum('valor') * 10 / 90)
            + abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'taxa_cliente_plataforma')->sum('valor'));

        // ── Creator ──────────────────────────────────────────────────────────
        // "Entradas" e "Comissão" continuam a reflectir o pagamento da
        // assinatura em si (dinheiro que entrou), mas "Saídas" tinha o mesmo
        // problema do Freelancing antes da correcção acima — contava
        // net_amount (quanto o criador tem a receber) já no momento do
        // pagamento, mesmo que ele nunca tenha sacado nada ainda.
        $crEntradas = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('amount');
        $crComissao = (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('platform_fee');
        $crSaidas   = abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'saque_aprovado')->where('fonte', 'assinaturas')->sum('valor'));

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
