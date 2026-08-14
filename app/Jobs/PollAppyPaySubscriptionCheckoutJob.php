<?php

namespace App\Jobs;

use App\Models\CreatorSubscriptionCheckout;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Rede de segurança para confirmar pagamentos AppyPay de assinaturas de
 * criador mesmo que o webhook falhe. Mesma lógica do PollAppyPayChargeJob,
 * mas para CreatorSubscriptionCheckout — reconciliação partilhada via
 * AppyPayReconciliationService (idempotente).
 */
class PollAppyPaySubscriptionCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public function __construct(
        private readonly CreatorSubscriptionCheckout $checkout,
        private readonly string $chargeId,
        private readonly string $paymentType // 'gpo' | 'ref'
    ) {}

    public function backoff(): array
    {
        return $this->paymentType === 'gpo'
            ? [120, 180, 300]
            : [1800, 3600, 21600];
    }

    public function retryUntil(): \DateTime
    {
        return $this->paymentType === 'gpo'
            ? now()->addMinutes(20)
            : now()->addDays(3);
    }

    public function handle(AppyPayGateway $gateway, AppyPayReconciliationService $reconciliation): void
    {
        $this->checkout->refresh();
        if ($this->checkout->payment_status === 'paid') {
            return;
        }

        $charge = $gateway->getCharge($this->chargeId);

        if (!$charge['success']) {
            Log::warning('PollAppyPaySubscriptionCheckoutJob: falha ao consultar estado', [
                'checkout_id' => $this->checkout->id,
                'charge_id'   => $this->chargeId,
            ]);
            throw new \RuntimeException('Falha ao consultar estado da cobrança AppyPay.');
        }

        $status = strtolower((string) $charge['status']);

        if (in_array($status, ['paid', 'completed', 'success', 'approved'], true)) {
            $amount = $charge['gateway_response']['payment']['amount'] ?? null;
            $reconciliation->markPaidByChargeId($this->chargeId, $amount !== null ? (float) $amount : null);
            return;
        }

        if (in_array($status, ['failed', 'rejected', 'declined', 'timeout', 'cancelled'], true)) {
            $reconciliation->markFailedByChargeId($this->chargeId, $status);
            return;
        }

        throw new \RuntimeException("Cobrança AppyPay {$this->chargeId} ainda pendente (estado: {$status}).");
    }

    public function failed(\Throwable $exception): void
    {
        $this->checkout->refresh();

        if ($this->checkout->payment_status === 'paid') {
            return;
        }

        app(AppyPayReconciliationService::class)->markFailedByChargeId($this->chargeId, 'timeout_polling');
    }
}
