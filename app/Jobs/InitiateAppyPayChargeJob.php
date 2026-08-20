<?php

namespace App\Jobs;

use App\Models\Service;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Payments\Services\AppyPayGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Cria a cobrança AppyPay (POST /v2.0/charges) em segundo plano — não bloqueia
 * o pedido web do cliente à espera da resposta.
 *
 * Motivo: confirmámos em produção (20/08/2026) que este endpoint pode demorar
 * mais do que qualquer tempo de espera razoável para um pedido HTTP síncrono
 * — no caso do Multicaixa Express (GPO), o pedido de aprovação chega ao
 * telemóvel do cliente e ele sai da nossa página para o aprovar lá, o que já
 * por si só pode demorar mais do que o timeout da nossa chamada. Fazer isto
 * de forma síncrona significa que a página do cliente falha exactamente
 * enquanto ele está a aprovar o pagamento no telemóvel — mesmo que o
 * pagamento se conclua a seguir com sucesso.
 *
 * Correndo em fila, o tempo de espera deste job não está ligado ao browser
 * do cliente nem ao limite de execução do PHP-FPM — só ao timeout do próprio
 * worker da fila. A vista de espera (PaymentEscrow::checkAppyPayStatus) faz
 * polling ao nosso serviço, não à AppyPay directamente, por isso não há
 * pressão de tempo do lado do cliente.
 */
class InitiateAppyPayChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Deve ficar sempre abaixo do --timeout do worker da fila (60s em produção). */
    public int $timeout = 55;
    public int $tries = 1;

    public function __construct(
        private readonly Service $service,
        private readonly string $paymentType, // 'gpo' | 'ref'
        private readonly ?string $phoneNumber,
        private readonly float $amount,
        private readonly string $merchantTransactionId
    ) {}

    public function handle(AppyPayGateway $gateway): void
    {
        $this->service->refresh();

        // Já iniciado/concluído por outra via (ex.: cliente tentou de novo
        // enquanto este job ainda corria) — nunca duplicar o pedido à AppyPay.
        if ($this->service->payment_status === 'paid' || $this->service->appypay_charge_id) {
            return;
        }

        $result = $this->paymentType === 'gpo'
            ? $gateway->chargeByPhone(
                $this->phoneNumber,
                $this->amount,
                'Pagamento de serviço #' . $this->service->id,
                $this->merchantTransactionId
            )
            : $gateway->chargeByReference(
                $this->amount,
                'Pagamento de serviço #' . $this->service->id,
                $this->merchantTransactionId
            );

        if (empty($result['success'])) {
            Log::error('InitiateAppyPayChargeJob: falha ao criar cobrança', [
                'service_id' => $this->service->id,
                'merchant_transaction_id' => $this->merchantTransactionId,
            ]);
            AuditLogger::log(
                'appypay_charge_ambiguous',
                "Pedido de cobrança AppyPay falhou/expirou para o serviço #{$this->service->id} (merchantTransactionId: {$this->merchantTransactionId}) — estado do pagamento do lado da AppyPay não confirmado, requer verificação manual.",
                'Service',
                $this->service->id
            );
            // Marca como falhado para o ecrã de espera do cliente (que está a
            // fazer polling ao nosso serviço) sair do estado de espera em vez
            // de ficar preso para sempre sem nunca receber um charge_id.
            $this->service->payment_status = 'failed';
            $this->service->save();
            return;
        }

        $this->service->payment_method_used = $this->paymentType === 'gpo' ? 'appypay_gpo' : 'appypay_ref';
        $this->service->appypay_charge_id   = $result['charge_id'];

        if ($this->paymentType === 'ref') {
            $referenceData = $result['gateway_response']['responseStatus']['reference'] ?? [];
            $this->service->payment_reference = $result['reference'] ?? ($referenceData['referenceNumber'] ?? null);
            $this->service->payment_entity    = $result['entity'] ?? null;
        }

        $this->service->save();

        PollAppyPayChargeJob::dispatch($this->service, $result['charge_id'], $this->paymentType)
            ->delay(now()->addSeconds(15));
    }

    /** Chamado quando o job falha (excepção não tratada, ou timeout do worker). */
    public function failed(\Throwable $exception): void
    {
        Log::error('InitiateAppyPayChargeJob: job falhou', [
            'service_id' => $this->service->id,
            'error'      => $exception->getMessage(),
        ]);
        AuditLogger::log(
            'appypay_charge_ambiguous',
            "Job de criação de cobrança AppyPay falhou para o serviço #{$this->service->id} (merchantTransactionId: {$this->merchantTransactionId}): {$exception->getMessage()}",
            'Service',
            $this->service->id
        );

        $this->service->refresh();
        if ($this->service->payment_status !== 'paid') {
            $this->service->payment_status = 'failed';
            $this->service->save();
        }
    }
}
