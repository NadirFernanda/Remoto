<?php

namespace App\Modules\Payments\Services;

/**
 * Gateway PayPal fictício para testes locais.
 *
 * Activo quando PAYPAL_MODE=fake (ou credenciais em branco).
 * Simula o fluxo completo sem chamar a API PayPal:
 *   createOrder  → redireciona para página local de aprovação
 *   getOrderStatus → devolve APPROVED para qualquer token FAKE-
 *   captureOrder   → devolve transaction_id fictício
 */
class FakePayPalGateway
{
    public function createOrder(float $amount, string $returnUrl, string $cancelUrl): array
    {
        $fakeToken = 'FAKE-' . strtoupper(uniqid());

        $approvalUrl = route('paypal.fake.approve', [
            'token'      => $fakeToken,
            'amount'     => $amount,
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return [
            'success'      => true,
            'approval_url' => $approvalUrl,
            'order_id'     => $fakeToken,
            'message'      => 'Ordem fictícia criada (modo de teste).',
        ];
    }

    public function getOrderStatus(string $orderId): array
    {
        return [
            'success' => true,
            'status'  => 'APPROVED',
            'message' => 'OK (simulado)',
        ];
    }

    public function captureOrder(string $orderId): array
    {
        return [
            'success'        => true,
            'transaction_id' => 'PAYPAL-FAKE-' . strtoupper(uniqid()),
            'message'        => 'Pagamento capturado (simulação).',
        ];
    }

    public function verifyWebhookSignature(array $headers, string $body, string $webhookId): bool
    {
        return true;
    }
}
