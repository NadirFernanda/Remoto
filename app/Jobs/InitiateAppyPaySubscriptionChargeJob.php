<?php

namespace App\Jobs;

use App\Models\CreatorSubscriptionCheckout;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Payments\Services\AppyPayGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Cria a cobrança AppyPay de uma assinatura em segundo plano — mesmo motivo do
 * InitiateAppyPayChargeJob (pagamento de projecto): o POST /v2.0/charges pode
 * demorar mais do que qualquer timeout HTTP síncrono razoável, e fazê-lo
 * dentro do próprio pedido web fazia a página do assinante falhar com um erro
 * ambíguo ("não foi possível confirmar a tempo") exactamente enquanto o
 * pagamento podia estar a ser aprovado no telemóvel dele.
 */
class InitiateAppyPaySubscriptionChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 55;
    public int $tries = 1;

    public function __construct(
        private readonly CreatorSubscriptionCheckout $checkout,
        private readonly string $paymentType, // 'gpo' | 'ref'
        private readonly ?string $phoneNumber,
        private readonly float $amount,
        private readonly string $merchantTransactionId
    ) {}

    public function handle(AppyPayGateway $gateway): void
    {
        $this->checkout->refresh();

        if ($this->checkout->payment_status === 'paid' || $this->checkout->appypay_charge_id) {
            return;
        }

        $result = $this->paymentType === 'gpo'
            ? $gateway->chargeByPhone(
                $this->phoneNumber,
                $this->amount,
                'Assinatura de criador — checkout #' . $this->checkout->id,
                $this->merchantTransactionId
            )
            : $gateway->chargeByReference(
                $this->amount,
                'Assinatura de criador — checkout #' . $this->checkout->id,
                $this->merchantTransactionId
            );

        if (empty($result['success'])) {
            Log::error('InitiateAppyPaySubscriptionChargeJob: falha ao criar cobrança', [
                'checkout_id' => $this->checkout->id,
                'merchant_transaction_id' => $this->merchantTransactionId,
            ]);
            AuditLogger::log(
                'appypay_charge_ambiguous',
                "Pedido de cobrança AppyPay falhou/expirou para o checkout de assinatura #{$this->checkout->id} (merchantTransactionId: {$this->merchantTransactionId}) — estado do pagamento do lado da AppyPay não confirmado, requer verificação manual.",
                'CreatorSubscriptionCheckout',
                $this->checkout->id
            );
            $this->checkout->payment_status = 'failed';
            $this->checkout->save();
            return;
        }

        $this->checkout->payment_method_used = $this->paymentType === 'gpo' ? 'appypay_gpo' : 'appypay_ref';
        $this->checkout->appypay_charge_id   = $result['charge_id'];

        if ($this->paymentType === 'ref') {
            $referenceData = $result['gateway_response']['responseStatus']['reference'] ?? [];
            $this->checkout->payment_reference = $result['reference'] ?? ($referenceData['referenceNumber'] ?? null);
            $this->checkout->payment_entity    = $result['entity'] ?? null;
        }

        $this->checkout->save();

        PollAppyPaySubscriptionCheckoutJob::dispatch($this->checkout, $result['charge_id'], $this->paymentType)
            ->delay(now()->addSeconds($this->paymentType === 'gpo' ? 15 : 30));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('InitiateAppyPaySubscriptionChargeJob: job falhou', [
            'checkout_id' => $this->checkout->id,
            'error'       => $exception->getMessage(),
        ]);
        AuditLogger::log(
            'appypay_charge_ambiguous',
            "Job de criação de cobrança AppyPay falhou para o checkout de assinatura #{$this->checkout->id} (merchantTransactionId: {$this->merchantTransactionId}): {$exception->getMessage()}",
            'CreatorSubscriptionCheckout',
            $this->checkout->id
        );

        $this->checkout->refresh();
        if ($this->checkout->payment_status !== 'paid') {
            $this->checkout->payment_status = 'failed';
            $this->checkout->save();
        }
    }
}
