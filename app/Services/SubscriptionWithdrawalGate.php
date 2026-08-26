<?php

namespace App\Services;

use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;

/**
 * Atribuição de saldo vindo de assinaturas de criador ainda não "resgatado"
 * por um saque marcado fonte=assinaturas.
 *
 * Não existe um "saldo de assinaturas" segregado — o saldo da carteira é
 * único e partilhado (ver fix do bug de saque duplicado). Isto não bloqueia
 * nem impõe um mínimo diferente: o saque no Painel Financeiro exige sempre
 * o mesmo mínimo geral (withdrawal_min_amount), independentemente da
 * origem do saldo. Esta classe serve só para marcar fonte='assinaturas' no
 * WalletLog do saque (FinancialPanel::solicitarSaque), usado pelo
 * CashFlowService para separar a fatia "Criador" da fatia "Freelancing"
 * nos relatórios do admin.
 */
class SubscriptionWithdrawalGate
{
    /** Fatia de ganhos de assinaturas que ainda não foi "consumida" por um saque marcado fonte=assinaturas. */
    public static function saldoAtribuivel(int $userId): float
    {
        $ganhoAssinaturas = (float) WalletLog::where('user_id', $userId)
            ->where('tipo', 'ganho_assinatura')
            ->sum('valor');

        // Conta tanto os pedidos ainda pendentes como os já aprovados — o
        // registo do saque muda de tipo no mesmo WalletLog quando o admin
        // aprova (ver Payouts::aprovarSaque), nunca cria uma linha nova.
        $jaSacadoAssinaturas = (float) WalletLog::where('user_id', $userId)
            ->whereIn('tipo', ['saque_solicitado', 'saque_aprovado'])
            ->where('fonte', 'assinaturas')
            ->sum(DB::raw('ABS(valor)'));

        return max(0, $ganhoAssinaturas - $jaSacadoAssinaturas);
    }
}
