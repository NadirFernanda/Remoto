<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\WalletLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InvalidateUnconfirmedServicePayment extends Command
{
    protected $signature = 'servicos:anular-pagamento-nao-confirmado
        {service : ID do projecto}
        {--apply : Grava a correcção; sem esta opção apenas pré-visualiza}';

    protected $description = 'Anula um pagamento marcado como pago sem confirmação real';

    public function handle(): int
    {
        $service = Service::find($this->argument('service'));
        if (!$service) {
            $this->error('Projecto não encontrado.');
            return self::FAILURE;
        }

        $logs = WalletLog::whereIn('tipo', ['escrow_retido', 'taxa_cliente_plataforma'])
            ->where('descricao', 'like', '%' . $service->titulo . '%')
            ->get();

        $this->line(sprintf(
            '#%d "%s" — estado: %s | pagamento: %s | lançamentos a anular: %d',
            $service->id,
            $service->titulo,
            $service->status,
            $service->payment_status,
            $logs->count()
        ));

        if (!$this->option('apply')) {
            $this->warn('Pré-visualização: use --apply para gravar a alteração.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($service, $logs): void {
            $locked = Service::whereKey($service->id)->lockForUpdate()->firstOrFail();
            $locked->payment_status = null;
            $locked->transaction_id = null;
            $locked->appypay_charge_id = null;
            $locked->appypay_merchant_transaction_id = null;
            $locked->payment_reference = null;
            $locked->payment_entity = null;
            $locked->is_payment_released = false;
            $locked->payment_released_at = null;
            $locked->status = 'negotiating';
            $locked->save();

            // WalletLog é imutável por regra; o soft delete remove o valor
            // dos relatórios sem destruir o histórico recuperável.
            foreach ($logs as $log) {
                $log->delete();
            }
        });

        $this->info('Pagamento anulado e lançamentos removidos dos relatórios.');
        return self::SUCCESS;
    }
}
