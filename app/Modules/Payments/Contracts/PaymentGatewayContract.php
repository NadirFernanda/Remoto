<?php

namespace App\Modules\Payments\Contracts;

/**
 * Contrato para gateways de pagamento.
 *
 * Implementações concretas:
 *   - SimulatedGateway   → modo de desenvolvimento/testes (nunca em produção)
 *   - MulticaixaGateway  → gateway ANGOLAN: Multicaixa Express / EMIS
 *   - StripeGateway      → cartões internacionais (VISA, Mastercard)
 *
 * Resolução automática via container Laravel:
 *   No AppServiceProvider ou PaymentsServiceProvider:
 *     $this->app->bind(PaymentGatewayContract::class, function () {
 *         return app()->environment('production')
 *             ? new MulticaixaGateway(config('services.multicaixa'))
 *             : new SimulatedGateway();
 *     });
 */
interface PaymentGatewayContract
{
    /**
     * Processa um pagamento.
     *
     * @param  array{
     *   amount: float,
     *   payment_token: string,   ← token opaco emitido pelo SDK front-end (NUNCA PAN/CVV)
     *   card_name?: string,
     *   description?: string,
     *   currency?: string,
     *   metadata?: array,
     * } $data
     *
     * @return array{
     *   success: bool,
     *   transaction_id: string|null,
     *   message: string,
     *   gateway_response?: array,
     * }
     */
    public function charge(array $data): array;

    /**
     * Processa um reembolso total ou parcial.
     *
     * @return array{success: bool, message: string}
     */
    public function refund(string $transactionId, float $amount): array;
}
