<?php

namespace App\Modules\Payments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Payments\Services\PayPalGateway;
use App\Models\Service;
use App\Services\FeeService;
use App\Services\AffiliateService;
use App\Jobs\NotifyFreelancersOfNewProject;

class PayPalController extends Controller
{
    /**
     * Recebe via POST a sessão do cliente, cria a PayPal Order e redireciona
     * o utilizador para a página de aprovação do PayPal.
     */
    public function create(Request $request)
    {
        $order = session('client_order', []);

        // Recupera o valor a cobrar
        $pagamento = $order['payment'] ?? null;
        $valor = (float)($pagamento['valor'] ?? 0);

        if ($valor <= 0) {
            return redirect()->route('client.payment', ['service' => $order['service_id'] ?? null])
                ->with('error', 'Valor inválido para pagamento PayPal.');
        }

        // Calcula total com taxa do cliente (10%)
        $fee = (new FeeService())->calculateServiceFee($valor);
        $valorTotal = round($fee['total_cliente'], 2);

        // Converte AOA → USD (taxa indicativa; em produção usar API de câmbio)
        // Por enquanto usa o valor directo em USD conforme configurado no .env PAYPAL_CURRENCY
        // Se a moeda for AOA, a converção deve ser feita aqui.
        $valorPayPal = $valorTotal;
        if (strtoupper(config('services.paypal.currency', 'USD')) === 'AOA') {
            // PayPal não aceita AOA — converter para USD
            $taxaCambio = (float) config('services.paypal.aoa_usd_rate', 0.0011);
            $valorPayPal = round($valorTotal * $taxaCambio, 2);
        }

        $returnUrl = route('paypal.capture');
        $cancelUrl = route('paypal.cancel');

        $gateway = new PayPalGateway();
        $result = $gateway->createOrder($valorPayPal, $returnUrl, $cancelUrl);

        if (!$result['success']) {
            return redirect()->route('client.payment', ['service' => $order['service_id'] ?? null])
                ->with('error', $result['message']);
        }

        // Guarda o ID da order PayPal na sessão para validar no retorno
        session(['paypal_order_id' => $result['order_id']]);

        return redirect()->away($result['approval_url']);
    }

    /**
     * URL de retorno após aprovação do utilizador no PayPal.
     * Captura o pagamento e publica o serviço.
     */
    public function capture(Request $request)
    {
        $orderId = $request->query('token');

        if (!$orderId) {
            return redirect()->route('client.payment')
                ->with('error', 'Referência de pagamento inválida.');
        }

        // Valida CSRF implícito: o order_id da sessão deve coincidir
        $sessionOrderId = session('paypal_order_id');
        if (!$sessionOrderId || $sessionOrderId !== $orderId) {
            return redirect()->route('client.payment')
                ->with('error', 'Pagamento não autorizado. Tente novamente.');
        }

        $gateway = new PayPalGateway();
        $result = $gateway->captureOrder($orderId);

        if (!$result['success']) {
            return redirect()->route('client.payment')
                ->with('error', $result['message']);
        }

        $transactionId = $result['transaction_id'];
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $order = session('client_order', []);
        $serviceId = $order['service_id'] ?? null;
        $service = $serviceId
            ? Service::where('id', $serviceId)->where('cliente_id', $user->id)->first()
            : null;

        $pagamento = $order['payment'] ?? null;
        $valor = (float)($pagamento['valor'] ?? 0);
        $fee = (new FeeService())->calculateServiceFee($valor);

        if ($service) {
            $service->valor          = $valor;
            $service->taxa           = $fee['taxa'];
            $service->valor_liquido  = $fee['valor_liquido'];
            $service->status         = 'published';
            $service->transaction_id = $transactionId;
            $service->save();
        } else {
            // Fallback: cria service a partir dos dados de sessão
            $briefing = $order['briefing_raw'] ?? null;
            $titulo = $order['title'] ?? null;

            if (!$briefing || !$titulo) {
                return redirect()->route('client.briefing')
                    ->with('error', 'Dados do pedido em falta. Por favor, preencha o briefing novamente.');
            }

            $briefingFinal = is_array($briefing)
                ? (isset($briefing['texto']) ? $briefing['texto'] : json_encode($briefing))
                : (string)$briefing;

            $service = Service::create([
                'cliente_id'     => $user->id,
                'titulo'         => $titulo,
                'briefing'       => $briefingFinal,
                'valor'          => $valor,
                'taxa'           => $fee['taxa'],
                'valor_liquido'  => $fee['valor_liquido'],
                'status'         => 'published',
                'transaction_id' => $transactionId,
            ]);
        }

        // Limpa sessão
        session()->forget(['client_order', 'briefing', 'briefing_title', 'paypal_order_id']);

        if ($service) {
            (new AffiliateService())->creditCommissionForReferredAction($user, 'publish_service', $service->id);
            NotifyFreelancersOfNewProject::dispatch($service);
        }

        return redirect()->route('client.orders')
            ->with('success', 'Pagamento via PayPal realizado e pedido publicado com sucesso!');
    }

    /**
     * URL de cancelamento — utilizador clicou em "Cancelar" no PayPal.
     */
    public function cancel(Request $request)
    {
        session()->forget('paypal_order_id');

        return redirect()->route('client.payment')
            ->with('error', 'Pagamento cancelado. Pode escolher outro método de pagamento.');
    }
}
