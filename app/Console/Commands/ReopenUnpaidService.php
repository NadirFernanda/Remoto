<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReopenUnpaidService extends Command
{
    protected $signature = 'servicos:reabrir-nao-pago
        {service : ID do projecto}
        {--apply : Grava a reparação; sem esta opção apenas pré-visualiza}';

    protected $description = 'Reabre como negociação um projecto entregue sem pagamento confirmado';

    public function handle(): int
    {
        $service = Service::find($this->argument('service'));

        if (!$service) {
            $this->error('Projecto não encontrado.');
            return self::FAILURE;
        }

        if ($service->payment_status === 'paid') {
            $this->error('Este projecto já está marcado como pago; nenhuma alteração foi feita.');
            return self::FAILURE;
        }

        if (!in_array($service->status, ['delivered', 'completed'], true)) {
            $this->error('O projecto não está em delivered/completed; nenhuma alteração foi feita.');
            return self::FAILURE;
        }

        $this->line(sprintf(
            '#%d "%s" — %s → negotiating | pagamento: %s',
            $service->id,
            $service->titulo,
            $service->status,
            $service->payment_status
        ));

        if (!$this->option('apply')) {
            $this->warn('Pré-visualização: use --apply para gravar a alteração.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($service): void {
            $locked = Service::whereKey($service->id)->lockForUpdate()->firstOrFail();

            if ($locked->payment_status === 'paid'
                || !in_array($locked->status, ['delivered', 'completed'], true)) {
                throw new \LogicException('O estado do projecto mudou; reparação cancelada.');
            }

            $locked->status = 'negotiating';
            $locked->is_payment_released = false;
            $locked->payment_released_at = null;
            $locked->save();
        });

        $this->info('Projecto reaberto como negotiating. O pagamento deve ser confirmado antes de continuar.');
        return self::SUCCESS;
    }
}
