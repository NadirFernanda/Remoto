<?php

namespace App\Services;

use App\Models\CashFlowClosing;
use Carbon\Carbon;

/**
 * Regista o fecho diário do fluxo de caixa — uma fotografia imutável do dia,
 * guardada para histórico/auditoria. Não bloqueia nem altera nenhuma operação
 * financeira já registada; é só um snapshot.
 *
 * Idempotente — fechar o mesmo dia duas vezes actualiza o registo existente em
 * vez de duplicar (útil para o fecho manual reflectir dados mais recentes do
 * mesmo dia, e para o comando agendado não falhar se já tiver corrido).
 */
class CashFlowClosingService
{
    public function __construct(private readonly CashFlowService $cashFlowService)
    {
    }

    public function closeDay(Carbon $date, string $fechadoPor = 'automatico'): CashFlowClosing
    {
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        $resultado = $this->cashFlowService->calculate($start, $end);

        $saldoAcumuladoAnterior = (float) CashFlowClosing::where('data', '<', $date->toDateString())
            ->orderByDesc('data')
            ->value('saldo_acumulado');

        $saldoAcumulado = $saldoAcumuladoAnterior + $resultado['saldoLiquido'];

        return CashFlowClosing::updateOrCreate(
            ['data' => $date->toDateString()],
            [
                'grupos'          => $resultado['grupos'],
                'total_entradas'  => $resultado['totalEntradas'],
                'total_saidas'    => $resultado['totalSaidas'],
                'total_comissao'  => $resultado['totalComissao'],
                'saldo_liquido'   => $resultado['saldoLiquido'],
                'saldo_acumulado' => $saldoAcumulado,
                'fechado_por'     => $fechadoPor,
            ]
        );
    }
}
