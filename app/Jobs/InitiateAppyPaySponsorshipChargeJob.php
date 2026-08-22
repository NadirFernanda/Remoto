<?php

namespace App\Jobs;

use App\Models\InfoprodutoPatrocinioCheckout;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Payments\Services\AppyPayGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Cria a cobrança AppyPay de um patrocínio de infoproduto em segundo plano —
 * mesmo motivo do InitiateAppyPayChargeJob (pagamento de projecto) e do
 * InitiateAppyPaySubscriptionChargeJob (assinaturas): o POST /v2.0/charges
 * pode demorar mais do que qualquer timeout HTTP síncrono razoável.
 */
class InitiateAppyPaySponsorshipChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 55;
    public int $tries = 1;

    public function __construct(
        private readonly InfoprodutoPatrocinioCheckout $checkout,
        private readonly string $phoneNumber,
        private readonly float $amount,
        private readonly string $description,
        private readonly string $merchantTransactionId
    ) {}

    public function handle(AppyPayGateway $gateway): void
    {
        $this->checkout->refresh();

        if ($this->checkout->payment_status === 'paid' || $this->checkout->appypay_charge_id) {
            return;
        }

        $result = $gateway->chargeByPhone(
            $this->phoneNumber,
            $this->amount,
            $this->description,
            $this->merchantTransactionId
        );

        if (empty($result['success'])) {
            Log::error('InitiateAppyPaySponsorshipChargeJob: falha ao criar cobrança', [
                'checkout_id' => $this->checkout->id,
                'merchant_transaction_id' => $this->merchantTransactionId,
            ]);
            AuditLogger::log(
                'appypay_charge_ambiguous',
                "Pedido de cobrança AppyPay falhou/expirou para o checkout de patrocínio #{$this->checkout->id} (merchantTransactionId: {$this->merchantTransactionId}) — estado do pagamento do lado da AppyPay não confirmado, requer verificação manual.",
                'InfoprodutoPatrocinioCheckout',
                $this->checkout->id
            );
            $this->checkout->payment_status = 'failed';
            $this->checkout->save();
            return;
        }

        $this->checkout->payment_method_used = 'appypay_gpo';
        $this->checkout->appypay_charge_id   = $result['charge_id'];
        $this->checkout->save();

        PollAppyPayInfoprodutoPatrocinioCheckoutJob::dispatch($this->checkout, $result['charge_id'])
            ->delay(now()->addSeconds(15));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('InitiateAppyPaySponsorshipChargeJob: job falhou', [
            'checkout_id' => $this->checkout->id,
            'error'       => $exception->getMessage(),
        ]);
        AuditLogger::log(
            'appypay_charge_ambiguous',
            "Job de criação de cobrança AppyPay falhou para o checkout de patrocínio #{$this->checkout->id} (merchantTransactionId: {$this->merchantTransactionId}): {$exception->getMessage()}",
            'InfoprodutoPatrocinioCheckout',
            $this->checkout->id
        );

        $this->checkout->refresh();
        if ($this->checkout->payment_status !== 'paid') {
            $this->checkout->payment_status = 'failed';
            $this->checkout->save();
        }
    }
}
