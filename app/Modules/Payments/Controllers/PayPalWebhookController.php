<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Notification;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Payments\Services\PayPalGateway;
use App\Services\FeeService;
use App\Jobs\NotifyFreelancersOfNewProject;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    /**
     * Endpoint de webhooks PayPal.
     * Configurar no PayPal Developer Portal → Webhooks → URL: /paypal/webhook
     *
     * Eventos tratados:
     *   PAYMENT.CAPTURE.COMPLETED  → reconcilia pagamentos que ficaram em limbo
     *   PAYMENT.CAPTURE.REVERSED   → chargeback: congela saldo do freelancer
     */
    public function handle(Request $request)
    {
        $webhookId = config('services.paypal.webhook_id', '');
        $rawBody   = $request->getContent();

        // ── Verificar assinatura (produção) ─────────────────────────────────
        if ($webhookId !== '') {
            try {
                $gateway = new PayPalGateway();
                $headers = array_change_key_case($request->headers->all(), CASE_UPPER);
                $headers = array_map(fn($v) => is_array($v) ? $v[0] : $v, $headers);

                if (!$gateway->verifyWebhookSignature($headers, $rawBody, $webhookId)) {
                    Log::warning('PayPal webhook: assinatura inválida', ['ip' => $request->ip()]);
                    return response()->json(['error' => 'Invalid signature'], 400);
                }
            } catch (\Throwable $e) {
                Log::error('PayPal webhook: erro na verificação', ['error' => $e->getMessage()]);
                // Em sandbox sem PAYPAL_WEBHOOK_ID, continua sem verificação
            }
        }

        $payload   = $request->json()->all();
        $eventType = $payload['event_type'] ?? '';

        Log::info('PayPal webhook recebido', ['event_type' => $eventType]);

        return match ($eventType) {
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($payload),
            'PAYMENT.CAPTURE.REVERSED'  => $this->handleCaptureReversed($payload),
            default                     => response()->json(['status' => 'ignored']),
        };
    }

    /**
     * PAYMENT.CAPTURE.COMPLETED
     * Reconcilia serviços que ficaram em payment_status = 'capturing' ou 'initiated'
     * devido a falha de rede após o PayPal já ter capturado.
     */
    private function handleCaptureCompleted(array $payload)
    {
        $resource      = $payload['resource'] ?? [];
        $captureId     = $resource['id'] ?? null;
        $supplementary = $resource['supplementary_data'] ?? [];
        $orderId       = $supplementary['related_ids']['order_id'] ?? null;

        if (!$orderId && !$captureId) {
            return response()->json(['status' => 'no_order_id'], 400);
        }

        $transactionId = 'PAYPAL-' . ($captureId ?? $orderId);

        DB::transaction(function () use ($orderId, $captureId, $transactionId, $payload) {
            // Tenta encontrar por paypal_order_id ou transaction_id
            $service = null;

            if ($orderId) {
                $service = Service::where('paypal_order_id', $orderId)->lockForUpdate()->first();
            }
            if (!$service && $captureId) {
                $service = Service::where('transaction_id', 'PAYPAL-' . $captureId)->lockForUpdate()->first();
            }

            if (!$service) {
                Log::warning('PayPal COMPLETED webhook: serviço não encontrado', [
                    'order_id'   => $orderId,
                    'capture_id' => $captureId,
                ]);
                return;
            }

            // Já está pago — nada a fazer
            if ($service->payment_status === 'paid') {
                return;
            }

            // Recuperar valor do payload
            $amount = (float)($payload['resource']['amount']['value'] ?? $service->valor ?? 0);

            $fee = (new FeeService())->calculateServiceFee($amount);

            $service->status          = 'published';
            $service->transaction_id  = $transactionId;
            $service->payment_status  = 'paid';
            $service->valor           = $amount;
            $service->taxa            = $fee['taxa'];
            $service->valor_liquido   = $fee['valor_liquido'];
            $service->save();

            Log::info('PayPal COMPLETED webhook: serviço reconciliado', ['service_id' => $service->id]);
            AuditLogger::log('paypal_webhook_reconciled', "Serviço #{$service->id} reconciliado via webhook COMPLETED", 'Service', $service->id);

            // Notificar freelancers se ainda não foi notificado
            if ($service->status === 'published') {
                NotifyFreelancersOfNewProject::dispatch($service);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    /**
     * PAYMENT.CAPTURE.REVERSED — Chargeback
     * O banco do cliente reverteu o pagamento. PayPal debitou a conta da plataforma.
     * Medida defensiva: congelar o saldo do freelancer que recebeu estes fundos.
     */
    private function handleCaptureReversed(array $payload)
    {
        $resource      = $payload['resource'] ?? [];
        $captureId     = $resource['id'] ?? null;
        $supplementary = $resource['supplementary_data'] ?? [];
        $orderId       = $supplementary['related_ids']['order_id'] ?? null;
        $amount        = (float)($resource['amount']['value'] ?? 0);

        DB::transaction(function () use ($orderId, $captureId, $amount, $payload) {
            $service = null;

            if ($orderId) {
                $service = Service::where('paypal_order_id', $orderId)->lockForUpdate()->first();
            }
            if (!$service && $captureId) {
                $service = Service::where('transaction_id', 'PAYPAL-' . $captureId)->lockForUpdate()->first();
            }

            if (!$service) {
                Log::warning('PayPal REVERSED webhook: serviço não encontrado', [
                    'order_id'   => $orderId,
                    'capture_id' => $captureId,
                ]);
                return;
            }

            // Marca o serviço como chargeback
            $service->payment_status = 'chargeback';
            $service->save();

            // Congelar saldo do freelancer se o pagamento já foi libertado
            if ($service->is_payment_released && $service->freelancer_id) {
                $freelancerWallet = Wallet::where('user_id', $service->freelancer_id)
                    ->lockForUpdate()
                    ->first();

                if ($freelancerWallet) {
                    $valorLiquido = (float)($service->valor_liquido ?? $amount * 0.8);
                    $amountToFreeze = min($valorLiquido, $freelancerWallet->saldo);

                    if ($amountToFreeze > 0) {
                        $freelancerWallet->decrement('saldo', $amountToFreeze);
                        $freelancerWallet->increment('saldo_pendente', $amountToFreeze);

                        WalletLog::create([
                            'user_id'   => $service->freelancer_id,
                            'wallet_id' => $freelancerWallet->id,
                            'valor'     => -$amountToFreeze,
                            'tipo'      => 'chargeback_congelado',
                            'fonte'     => 'projetos',
                            'descricao' => "Chargeback do cliente no serviço #{$service->id} — saldo retido para análise.",
                        ]);
                    }
                }

                Notification::create([
                    'user_id' => $service->freelancer_id,
                    'type'    => 'chargeback',
                    'title'   => 'Alerta: chargeback detectado',
                    'message' => "O pagamento do projeto \"{$service->titulo}\" foi estornado pelo cliente. O teu saldo foi temporariamente retido para análise. Entra em contacto com o suporte.",
                ]);
            }

            // Notificar o cliente também
            if ($service->cliente_id) {
                Notification::create([
                    'user_id' => $service->cliente_id,
                    'type'    => 'chargeback',
                    'title'   => 'Estorno processado',
                    'message' => "O estorno do projeto \"{$service->titulo}\" foi registado. A nossa equipa irá analisar a situação.",
                ]);
            }

            AuditLogger::log(
                'chargeback',
                "Chargeback no serviço #{$service->id} — valor: {$amount} — freelancer: {$service->freelancer_id}",
                'Service',
                $service->id
            );

            Log::warning('PayPal REVERSED webhook: chargeback processado', [
                'service_id'   => $service->id,
                'amount'       => $amount,
                'freelancer_id' => $service->freelancer_id,
            ]);
        });

        return response()->json(['status' => 'ok']);
    }
}
