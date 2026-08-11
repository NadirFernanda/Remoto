<?php

namespace App\Modules\Payments\Services;

use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\Service;
use App\Modules\Admin\Services\AuditLogger;
use App\Services\AffiliateService;
use App\Services\FeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lógica partilhada de reconciliação de pagamentos AppyPay — chamada tanto pelo
 * webhook (App­yPayWebhookController) como pelo job de polling (PollAppyPayChargeJob),
 * para nunca duplicar o efeito de marcar um serviço como pago.
 */
class AppyPayReconciliationService
{
    /**
     * Marca um serviço como pago a partir do ID da cobrança AppyPay.
     * Idempotente — se já estiver pago, não faz nada.
     */
    public function markPaidByChargeId(string $chargeId, ?float $amountFromGateway = null): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $service = Service::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$service) {
                Log::warning('AppyPay: serviço não encontrado para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($service->payment_status === 'paid') {
                return;
            }

            $amount = $amountFromGateway ?? (float)($service->valor ?? 0);
            $fee    = (new FeeService())->calculateServiceFee($amount);

            $service->status         = 'published';
            $service->payment_status = 'paid';
            $service->transaction_id = 'APPYPAY-' . $chargeId;
            $service->valor          = $amount;
            $service->taxa           = $fee['taxa'];
            $service->valor_liquido  = $fee['valor_liquido'];
            $service->save();

            Log::info('AppyPay: serviço reconciliado', ['service_id' => $service->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_payment_confirmed', "Pagamento AppyPay confirmado para o serviço #{$service->id}", 'Service', $service->id);

            (new AffiliateService())->creditCommissionForReferredAction($service->cliente, 'publish_service', $service->id);
            NotifyFreelancersOfNewProject::dispatch($service);
        });
    }

    /** Marca um serviço como falhado (pagamento rejeitado, saldo insuficiente, timeout). */
    public function markFailedByChargeId(string $chargeId, string $reason = ''): void
    {
        $service = Service::where('appypay_charge_id', $chargeId)->first();

        if (!$service || $service->payment_status === 'paid') {
            return;
        }

        $service->payment_status = 'failed';
        $service->save();

        Log::info('AppyPay: pagamento falhado', ['service_id' => $service->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_payment_failed', "Pagamento AppyPay falhou para o serviço #{$service->id}: {$reason}", 'Service', $service->id);
    }
}
