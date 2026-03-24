<?php

namespace App\Modules\Payments\Services;

use App\Modules\Payments\Contracts\PaymentGatewayContract;

/**
 * Gateway de pagamento simulado — APENAS PARA DESENVOLVIMENTO E TESTES.
 *
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║  ATENÇÃO: Este gateway APROVA TODOS os pagamentos automaticamente.  ║
 * ║  NUNCA deve ser activo em ambiente de produção.                     ║
 * ║  Em produção, substituir por MulticaixaGateway ou StripeGateway.   ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 *
 * Para activar um gateway real, registar no PaymentsServiceProvider:
 *
 *   $this->app->bind(PaymentGatewayContract::class, function () {
 *       return app()->environment('production')
 *           ? new MulticaixaGateway(config('services.multicaixa'))
 *           : new PaymentGateway();
 *   });
 *
 * E injectar via contrato:
 *   public function __construct(private PaymentGatewayContract $gateway) {}
 */
class PaymentGateway implements PaymentGatewayContract
{
    /**
     * Simula um pagamento. Sempre aprovado em modo de desenvolvimento.
     *
     * SEGURANÇA: o campo 'payment_token' deve conter apenas o token opaco
     * gerado pelo SDK front-end do gateway. Nunca aceitar PAN, CVV ou expiry
     * directamente neste método — essa validação é feita no Livewire component.
     */
    public function charge(array $data): array
    {
        if (app()->environment('production')) {
            \Log::critical('PaymentGateway simulado activo em PRODUÇÃO — integrar gateway real imediatamente!', [
                'amount' => $data['amount'] ?? null,
            ]);
        }

        return [
            'success'        => true,
            'transaction_id' => 'SIM-' . strtoupper(uniqid()),
            'message'        => 'Pagamento aprovado (simulação)',
        ];
    }

    /**
     * Simula um reembolso. Sempre aprovado em modo de desenvolvimento.
     */
    public function refund(string $transactionId, float $amount): array
    {
        return [
            'success' => true,
            'message' => "Reembolso de {$amount} simulado para transacção {$transactionId}",
        ];
    }

}


