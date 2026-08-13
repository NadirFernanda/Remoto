<?php

namespace App\Console\Commands;

use App\Services\CashFlowClosingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Artisan command: regista o fecho diário do fluxo de caixa.
 *
 * Agendamento recomendado: diariamente às 23:59 (Kernel.php).
 * Execução manual: php artisan cashflow:close [data]
 */
class CloseCashFlowDay extends Command
{
    protected $signature   = 'cashflow:close {date? : Data a fechar (Y-m-d), por omissão hoje}';
    protected $description = 'Regista o fecho diário do fluxo de caixa (snapshot histórico, não bloqueia nada)';

    public function handle(CashFlowClosingService $service): int
    {
        $date = $this->argument('date')
            ? Carbon::parse($this->argument('date'))
            : Carbon::today();

        $fecho = $service->closeDay($date);

        $this->info("Fecho de {$date->toDateString()} registado — saldo do dia: {$fecho->saldo_liquido}, acumulado: {$fecho->saldo_acumulado}");
        return self::SUCCESS;
    }
}
