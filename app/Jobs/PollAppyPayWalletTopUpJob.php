<?php

namespace App\Jobs;

use App\Models\WalletTopUp;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Rede de segurança para confirmar recargas de carteira via AppyPay (Multicaixa
 * Express) mesmo que o webhook falhe ou ainda não esteja configurado do lado da
 * AppyPay. Mesma lógica do PollAppyPayChargeJob, mas para WalletTopUp em vez de
 * Service — reconciliação partilhada via AppyPayReconciliationService (idempotente).
 */
class PollAppyPayWalletTopUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public function __construct(
        private readonly WalletTopUp $topUp,
        private readonly string $chargeId
    ) {}

    public function backoff(): array
    {
        return [120, 180, 300]; // 2min, 3min, depois a cada 5min
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(20);
    }

    public function handle(AppyPayGateway $gateway, AppyPayReconciliationService $reconciliation): void
    {
        $this->topUp->refresh();
        if ($this->topUp->payment_status === 'paid') {
            return;
        }

        $charge = $gateway->getCharge($this->chargeId);

        if (!$charge['success']) {
            Log::warning('PollAppyPayWalletTopUpJob: falha ao consultar estado', [
                'top_up_id' => $this->topUp->id,
                'charge_id' => $this->chargeId,
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
            return; // estado final — não faz sentido continuar a tentar
        }

        // Ainda pendente — lança excepção para o Laravel reagendar via backoff().
        throw new \RuntimeException("Cobrança AppyPay {$this->chargeId} ainda pendente (estado: {$status}).");
    }

    /** Chamado quando retryUntil() expira sem confirmação — marca como falhado por timeout. */
    public function failed(\Throwable $exception): void
    {
        $this->topUp->refresh();

        if ($this->topUp->payment_status === 'paid') {
            return;
        }

        app(AppyPayReconciliationService::class)->markFailedByChargeId($this->chargeId, 'timeout_polling');
    }
}
