<?php

namespace App\Modules\Payments\Controllers;

use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class AppyPayWebhookController extends Controller
{
    /**
     * Endpoint de webhooks AppyPay.
     *
     * IMPORTANTE: a documentação fornecida pela AppyPay não especifica o formato
     * exacto do payload nem o mecanismo de assinatura do webhook — o comercial
     * (Edson Fonseca, comercial@appy.co.ao) pediu para avisarmos quando este
     * endpoint estiver pronto, para nos confirmarem esses detalhes e activarem
     * o webhook do lado deles. Por agora:
     *   - Se APPYPAY_WEBHOOK_SECRET estiver definido, exige-o num header
     *     (X-Appy-Signature) — ajustar o nome do header assim que a AppyPay
     *     confirmar o mecanismo real.
     *   - O payload é lido de forma defensiva (aceita algumas formas plausíveis
     *     de indicar o ID e o estado da cobrança), porque ainda não vimos um
     *     exemplo real do corpo do webhook.
     *   - Cada evento é sempre confirmado com um GET /charges/{id} antes de
     *     marcar como pago — nunca confiamos cegamente no conteúdo do webhook.
     */
    public function handle(Request $request, AppyPayReconciliationService $reconciliation)
    {
        $secret = config('services.appypay.webhook_secret', '');

        if ($secret !== '') {
            $signature = $request->header('X-Appy-Signature', '');
            if (!hash_equals($secret, (string) $signature)) {
                Log::warning('AppyPay webhook: assinatura inválida', ['ip' => $request->ip()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $payload = $request->json()->all();
        Log::info('AppyPay webhook recebido', ['payload' => $payload]);

        $chargeId = $payload['id'] ?? $payload['chargeId'] ?? $payload['data']['id'] ?? null;
        $status   = strtolower((string) ($payload['status'] ?? $payload['data']['status'] ?? ''));

        if (!$chargeId) {
            Log::warning('AppyPay webhook: sem ID de cobrança no payload', ['payload' => $payload]);
            return response()->json(['status' => 'ignored']);
        }

        // Nunca confiar cegamente no webhook — confirmar sempre o estado real via API.
        $gateway = app(\App\Modules\Payments\Services\AppyPayGateway::class);
        $charge  = $gateway->getCharge($chargeId);

        if (!$charge['success']) {
            Log::warning('AppyPay webhook: falha ao confirmar estado da cobrança', ['charge_id' => $chargeId]);
            return response()->json(['status' => 'retry_later'], 202);
        }

        $confirmedStatus = strtolower((string) $charge['status']);

        if (in_array($confirmedStatus, ['paid', 'completed', 'success', 'approved'], true)) {
            $amount = $charge['gateway_response']['payment']['amount'] ?? null;
            $reconciliation->markPaidByChargeId($chargeId, $amount !== null ? (float) $amount : null);
        } elseif (in_array($confirmedStatus, ['failed', 'rejected', 'declined', 'timeout', 'cancelled'], true)) {
            $reconciliation->markFailedByChargeId($chargeId, $confirmedStatus);
        }

        return response()->json(['status' => 'ok']);
    }
}
