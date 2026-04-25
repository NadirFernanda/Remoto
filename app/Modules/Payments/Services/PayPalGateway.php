<?php

namespace App\Modules\Payments\Services;

use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Environment;

/**
 * Gateway PayPal — integração real via PayPal REST API v2.
 *
 * Fluxo:
 *   1. createOrder()  → devolve URL de aprovação do PayPal
 *   2. Utilizador aprova no PayPal e é redirecionado para /paypal/capture?token=...
 *   3. captureOrder() → confirma o pagamento e devolve transaction_id
 */
class PayPalGateway
{
    private $client;
    private string $currency;

    public function __construct()
    {
        $cfg = config('services.paypal');

        $clientId     = (string)($cfg['client_id'] ?? '');
        $clientSecret = (string)($cfg['client_secret'] ?? '');

        if ($clientId === '' || $clientSecret === '') {
            throw new \RuntimeException(
                'Credenciais PayPal não configuradas. Defina PAYPAL_CLIENT_ID e PAYPAL_CLIENT_SECRET no ficheiro .env.'
            );
        }

        $env = ($cfg['mode'] === 'live')
            ? Environment::PRODUCTION
            : Environment::SANDBOX;

        $this->client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init($clientId, $clientSecret)
            )
            ->environment($env)
            ->build();

        $this->currency = strtoupper($cfg['currency'] ?? 'USD');
    }

    /**
     * Cria uma PayPal Order e devolve a URL de aprovação.
     *
     * @return array{success: bool, approval_url: string|null, order_id: string|null, message: string}
     */
    public function createOrder(float $amount, string $returnUrl, string $cancelUrl): array
    {
        try {
            $amountStr = number_format($amount, 2, '.', '');

            $orderRequest = OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [
                    PurchaseUnitRequestBuilder::init(
                        AmountWithBreakdownBuilder::init($this->currency, $amountStr)->build()
                    )->build(),
                ]
            )
            ->applicationContext(
                OrderApplicationContextBuilder::init()
                    ->returnUrl($returnUrl)
                    ->cancelUrl($cancelUrl)
                    ->build()
            )
            ->build();

            $response = $this->client->getOrdersController()->createOrder([
                'body' => $orderRequest,
            ]);

            $order = $response->getResult();
            $approvalUrl = null;

            foreach ($order->getLinks() as $link) {
                if ($link->getRel() === 'payer-action' || $link->getRel() === 'approve') {
                    $approvalUrl = $link->getHref();
                    break;
                }
            }

            return [
                'success'      => true,
                'approval_url' => $approvalUrl,
                'order_id'     => $order->getId(),
                'message'      => 'Order criada com sucesso.',
            ];
        } catch (\Throwable $e) {
            \Log::error('PayPal createOrder falhou', ['error' => $e->getMessage()]);
            return [
                'success'      => false,
                'approval_url' => null,
                'order_id'     => null,
                'message'      => 'Erro ao criar pagamento PayPal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Captura uma PayPal Order depois da aprovação do utilizador.
     *
     * @return array{success: bool, transaction_id: string|null, message: string}
     */
    public function captureOrder(string $orderId): array
    {
        try {
            $response = $this->client->getOrdersController()->captureOrder([
                'id' => $orderId,
            ]);

            $result = $response->getResult();
            $captures = $result->getPurchaseUnits()[0]->getPayments()->getCaptures() ?? [];
            $captureId = !empty($captures) ? $captures[0]->getId() : $orderId;

            return [
                'success'        => true,
                'transaction_id' => 'PAYPAL-' . $captureId,
                'message'        => 'Pagamento capturado com sucesso.',
            ];
        } catch (\Throwable $e) {
            \Log::error('PayPal captureOrder falhou', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return [
                'success'        => false,
                'transaction_id' => null,
                'message'        => 'Erro ao confirmar pagamento PayPal: ' . $e->getMessage(),
            ];
        }
    }
}
