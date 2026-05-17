<?php

namespace App\Modules\Payments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $pagamento  = $order['payment'] ?? null;
        $valor      = (float)($pagamento['valor'] ?? 0);
        $serviceId  = $order['service_id'] ?? null;

        if ($valor <= 0) {
            return redirect()->route('client.payment', ['service' => $serviceId])
                ->with('error', 'Valor inválido para pagamento PayPal.');
        }

        $fee        = (new FeeService())->calculateServiceFee($valor);
        $valorTotal = round($fee['total_cliente'], 2);

        $valorPayPal = $valorTotal;
        if (strtoupper(config('services.paypal.currency', 'USD')) === 'AOA') {
            $taxaCambio  = (float) config('services.paypal.aoa_usd_rate', 0.0011);
            $valorPayPal = round($valorTotal * $taxaCambio, 2);
        }

        $returnUrl = route('paypal.capture');
        $cancelUrl = route('paypal.cancel');

        $gateway = PayPalGateway::make();
        $result  = $gateway->createOrder($valorPayPal, $returnUrl, $cancelUrl);

        if (!$result['success']) {
            return redirect()->route('client.payment', ['service' => $serviceId])
                ->with('error', $result['message']);
        }

        $paypalOrderId = $result['order_id'];

        // Guarda o ID da order PayPal na sessão para validar no retorno (CSRF implícito)
        session(['paypal_order_id' => $paypalOrderId]);

        // Persiste o paypal_order_id no registo de serviço (se já existir) para reconciliação
        if ($serviceId) {
            Service::where('id', $serviceId)
                ->where('cliente_id', Auth::id())
                ->whereIn('payment_status', ['none', 'initiated', 'failed'])
                ->update([
                    'paypal_order_id' => $paypalOrderId,
                    'payment_status'  => 'initiated',
                ]);
        }

        return redirect()->away($result['approval_url']);
    }

    /**
     * URL de retorno após aprovação do utilizador no PayPal.
     * Captura o pagamento e publica o serviço.
     * Protegido contra double-capture por DB lock + verificação de payment_status.
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

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // ── Idempotência: já foi pago por este paypal_order_id? ──────────────
        $existingPaid = Service::where('paypal_order_id', $orderId)
            ->where('payment_status', 'paid')
            ->first();

        if ($existingPaid) {
            session()->forget(['client_order', 'briefing', 'briefing_title', 'paypal_order_id']);
            return redirect()->route('client.orders')
                ->with('success', 'Pagamento já processado. O teu pedido está publicado.');
        }

        // ── Verificar se a ordem PayPal não expirou ──────────────────────────
        $gateway     = PayPalGateway::make();
        $statusCheck = $gateway->getOrderStatus($orderId);

        if ($statusCheck['success'] && !in_array($statusCheck['status'], ['APPROVED', 'COMPLETED', 'CREATED'])) {
            return redirect()->route('client.payment')
                ->with('error', 'O pagamento expirou ou foi cancelado. Por favor, inicia um novo pagamento.');
        }

        // ── Captura com protecção contra race condition ───────────────────────
        $transactionId = null;
        $service       = null;

        try {
            DB::transaction(function () use ($orderId, $user, &$transactionId, &$service) {
                // Bloqueia o registo de serviço (se existir) para evitar double-capture
                $existing = Service::where('paypal_order_id', $orderId)
                    ->where('cliente_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing && $existing->payment_status === 'paid') {
                    $service = $existing;
                    return; // já processado por outro request concorrente
                }

                if ($existing) {
                    $existing->payment_status = 'capturing';
                    $existing->save();
                }

                // Captura no PayPal
                $result = PayPalGateway::make()->captureOrder($orderId);

                if (!$result['success']) {
                    if ($existing) {
                        $existing->payment_status = 'failed';
                        $existing->save();
                    }
                    throw new \RuntimeException($result['message']);
                }

                $transactionId = $result['transaction_id'];

                $order     = session('client_order', []);
                $pagamento = $order['payment'] ?? null;
                $valor     = (float)($pagamento['valor'] ?? 0);
                $fee       = (new FeeService())->calculateServiceFee($valor);

                if ($existing) {
                    $existing->valor          = $valor;
                    $existing->taxa           = $fee['taxa'];
                    $existing->valor_liquido  = $fee['valor_liquido'];
                    $existing->status         = 'published';
                    $existing->transaction_id = $transactionId;
                    $existing->payment_status = 'paid';
                    $existing->save();
                    $service = $existing;
                } else {
                    // Fallback: cria service a partir dos dados de sessão
                    $serviceId  = $order['service_id'] ?? null;
                    $sessionSvc = $serviceId
                        ? Service::where('id', $serviceId)->where('cliente_id', $user->id)->first()
                        : null;

                    if ($sessionSvc) {
                        $sessionSvc->valor          = $valor;
                        $sessionSvc->taxa           = $fee['taxa'];
                        $sessionSvc->valor_liquido  = $fee['valor_liquido'];
                        $sessionSvc->status         = 'published';
                        $sessionSvc->transaction_id = $transactionId;
                        $sessionSvc->payment_status = 'paid';
                        $sessionSvc->paypal_order_id = $orderId;
                        $sessionSvc->save();
                        $service = $sessionSvc;
                    } else {
                        $briefing = $order['briefing_raw'] ?? null;
                        $titulo   = $order['title'] ?? null;

                        if (!$briefing || !$titulo) {
                            throw new \RuntimeException('Dados do pedido em falta. Preenche o briefing novamente.');
                        }

                        $briefingFinal = is_array($briefing)
                            ? (isset($briefing['texto']) ? $briefing['texto'] : json_encode($briefing))
                            : (string)$briefing;

                        $service = Service::create([
                            'cliente_id'      => $user->id,
                            'titulo'          => $titulo,
                            'briefing'        => $briefingFinal,
                            'valor'           => $valor,
                            'taxa'            => $fee['taxa'],
                            'valor_liquido'   => $fee['valor_liquido'],
                            'status'          => 'published',
                            'transaction_id'  => $transactionId,
                            'payment_status'  => 'paid',
                            'paypal_order_id' => $orderId,
                        ]);
                    }
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('client.payment')
                ->with('error', $e->getMessage());
        }

        // Se já estava pago (concorrência), redireciona igualmente
        if ($service && $service->payment_status === 'paid') {
            session()->forget(['client_order', 'briefing', 'briefing_title', 'paypal_order_id']);
            (new AffiliateService())->creditCommissionForReferredAction($user, 'publish_service', $service->id);
            NotifyFreelancersOfNewProject::dispatch($service);
        }

        session()->forget(['client_order', 'briefing', 'briefing_title', 'paypal_order_id']);

        return redirect()->route('client.orders')
            ->with('success', 'Pagamento via PayPal realizado e pedido publicado com sucesso!');
    }

    /**
     * Página de aprovação fictícia — apenas activa quando PAYPAL_MODE=fake.
     * Mostra botões "Aprovar" e "Cancelar" para simular o fluxo PayPal localmente.
     */
    public function fakeApprove(Request $request)
    {
        abort_if(config('services.paypal.mode') !== 'fake' && !empty(config('services.paypal.client_id')), 404);

        return view('payments.paypal-fake-approve', [
            'token'      => $request->query('token'),
            'amount'     => $request->query('amount'),
            'return_url' => $request->query('return_url'),
            'cancel_url' => $request->query('cancel_url'),
        ]);
    }

    /**
     * URL de cancelamento — utilizador clicou em "Cancelar" no PayPal.
     */
    public function cancel(Request $request)
    {
        $orderId = session('paypal_order_id');

        // Marca o serviço como falhou (se existia registo iniciado)
        if ($orderId) {
            Service::where('paypal_order_id', $orderId)
                ->where('payment_status', 'initiated')
                ->update(['payment_status' => 'failed']);
        }

        session()->forget('paypal_order_id');

        return redirect()->route('client.payment')
            ->with('error', 'Pagamento cancelado. Pode escolher outro método de pagamento.');
    }
}
