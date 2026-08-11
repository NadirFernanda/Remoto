<?php

namespace App\Jobs;

use App\Models\Service;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Rede de segurança para confirmar pagamentos AppyPay mesmo que o webhook falhe
 * ou ainda não esteja configurado do lado da AppyPay. Consulta GET /charges/{id}
 * periodicamente e reconcilia via AppyPayReconciliationService (idempotente,
 * partilhada com o webhook — nunca marca o mesmo serviço como pago duas vezes).
 *
 * GPO (telefone): aprovação costuma demorar segundos/minutos — tentativas
 * frequentes durante ~20 minutos.
 * REF (referência): o cliente paga fora da plataforma, pode demorar dias —
 * tentativas espaçadas ao longo de alguns dias antes de expirar.
 */
class PollAppyPayChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public function __construct(
        private readonly Service $service,
        private readonly string $chargeId,
        private readonly string $paymentType // 'gpo' | 'ref'
    ) {}

    public function backoff(): array
    {
        return $this->paymentType === 'gpo'
            ? [120, 180, 300]           // 2min, 3min, depois a cada 5min
            : [1800, 3600, 21600];      // 30min, 1h, depois a cada 6h
    }

    public function retryUntil(): \DateTime
    {
        return $this->paymentType === 'gpo'
            ? now()->addMinutes(20)
            : now()->addDays(3);
    }

    public function handle(AppyPayGateway $gateway, AppyPayReconciliationService $reconciliation): void
    {
        // Já reconciliado por outra via (webhook ou tentativa anterior) — nada a fazer.
        $this->service->refresh();
        if ($this->service->payment_status === 'paid') {
            return;
        }

        $charge = $gateway->getCharge($this->chargeId);

        if (!$charge['success']) {
            Log::warning('PollAppyPayChargeJob: falha ao consultar estado', [
                'service_id' => $this->service->id,
                'charge_id'  => $this->chargeId,
            ]);
            throw new \RuntimeException('Falha ao consultar estado da cobrança AppyPay.');
        }

        $status = strtolower((string) $charge['status']);

        if (in_array($status, ['paid', 'completed', 'success', 'approved'], true)) {
            $amount = $charge['gateway_response']['amount'] ?? null;
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
        $this->service->refresh();

        if ($this->service->payment_status === 'paid') {
            return;
        }

        app(AppyPayReconciliationService::class)->markFailedByChargeId($this->chargeId, 'timeout_polling');
    }
}
