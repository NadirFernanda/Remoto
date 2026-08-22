<?php

namespace App\Services;

use App\Models\WalletLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Regra especial para saldo ainda por resgatar vindo de assinaturas de
 * criador: enquanto houver alguma fatia de "ganho_assinatura" ainda não
 * coberta por um saque marcado fonte=assinaturas, o saque no Painel
 * Financeiro fica sujeito a um mínimo maior e a um intervalo mínimo entre
 * pedidos.
 *
 * Não existe um "saldo de assinaturas" segregado — o saldo da carteira é
 * único e partilhado (ver fix do bug de saque duplicado). Isto é só um
 * controlo de quando/quanto pode ser sacado, aplicado sobre o saque único
 * já existente (FinancialPanel::solicitarSaque), nunca um fluxo de saque à
 * parte.
 */
class SubscriptionWithdrawalGate
{
    public const SAQUE_MINIMO = 20000.0;

    // TEMPORÁRIO — desactivado a pedido para testar o fluxo de saque/IBAN em
    // produção sem esperar 14 dias. REPOR PARA 14 DEPOIS DO TESTE.
    public const COOLDOWN_DIAS = 0;

    /** Fatia de ganhos de assinaturas que ainda não foi "consumida" por um saque gated. */
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

    /** Dias que faltam até o próximo saque gated ser permitido (0 = já pode). */
    public static function diasParaProximoSaque(int $userId): int
    {
        $ultimo = WalletLog::where('user_id', $userId)
            ->where('fonte', 'assinaturas')
            ->whereIn('tipo', ['saque_solicitado', 'saque_aprovado'])
            ->latest()
            ->first();

        if (!$ultimo) {
            return 0;
        }

        $decorridos = (int) Carbon::parse($ultimo->created_at)->diffInDays(now());

        return max(0, self::COOLDOWN_DIAS - $decorridos);
    }
}
