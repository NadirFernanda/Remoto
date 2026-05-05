<?php

namespace App\Livewire\Client;

use App\Events\PaymentFailed;
use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\Service;
use App\Modules\Payments\Services\PaymentGateway;
use App\Services\AffiliateService;
use App\Services\FeeService;
use App\Traits\UserSessionTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentEscrow extends Component
{
    use UserSessionTrait;

    public float $valor        = 10000;
    public float $taxa_cliente = 0.0;
    public float $valor_total  = 0.0;
    public float $taxa         = 0.0;
    public float $valor_liquido = 0;

    public string $payment_method = 'card';

    public $listeners = ['updatedPaymentMethod' => 'render'];

    // Campos de UI apenas — nunca persistidos. Em produção o gateway front-end
    // gera um token opaco e escreve em $payment_token via Livewire.
    public string $card_name    = '';
    public string $card_number  = '';
    public string $card_expiry  = '';
    public string $card_cvv     = '';
    public string $payment_token = '';

    public function mount(): void
    {
        $order    = session('client_order', []);
        $pagamento = $order['payment'] ?? session('pagamento', null);

        $this->valor = $pagamento
            ? (float)($pagamento['valor'] ?? 10000)
            : (float)request()->query('valor', 10000);

        $fee = (new FeeService())->calculateServiceFee($this->valor);
        $this->taxa_cliente  = $fee['taxa_cliente'];
        $this->valor_total   = $fee['total_cliente'];
        $this->taxa          = $fee['taxa'];
        $this->valor_liquido = $fee['valor_liquido'];
    }

    public function confirmPayment()
    {
        // ── PayPal / métodos indisponíveis ───────────────────────────────────
        if ($this->payment_method === 'paypal') {
            return redirect()->route('paypal.create');
        }
        if (in_array($this->payment_method, ['express', 'bank'])) {
            session()->flash('error', 'Este método de pagamento ainda não está disponível. Escolhe outro.');
            return;
        }

        // ── Autenticação ─────────────────────────────────────────────────────
        $user = $this->getCurrentUser();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        // ── Validação do cartão ──────────────────────────────────────────────
        $this->validate([
            'payment_token' => 'required|string|min:8',
            'card_name'     => 'required|string|min:3|max:100',
        ], [
            'payment_token.required' => 'Erro ao processar o cartão. Tente novamente.',
            'card_name.required'     => 'Informe o nome do titular do cartão.',
        ]);

        // ── Recuperar ou criar o registo de serviço (estado: payment_pending) ─
        $order     = session('client_order', []);
        $serviceId = $order['service_id'] ?? null;
        $service   = $serviceId
            ? Service::where('id', $serviceId)->where('cliente_id', $user->id)->first()
            : null;

        if ($service) {
            $service->valor          = $this->valor;
            $service->taxa           = $this->taxa;
            $service->valor_liquido  = $this->valor_liquido;
            $service->payment_status = 'initiated';
            $service->status         = 'payment_pending';
            $service->save();
        } else {
            $briefing = $order['briefing_raw'] ?? session('briefing', null);
            $titulo   = $order['title'] ?? session('briefing_title');

            if (!$briefing || !$titulo) {
                session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
                return redirect()->route('client.briefing');
            }

            $briefingFinal = is_array($briefing)
                ? ($briefing['texto'] ?? json_encode($briefing))
                : (string)$briefing;

            $service = Service::create([
                'cliente_id'     => $user->id,
                'titulo'         => is_string($titulo) ? trim($titulo) : '',
                'briefing'       => $briefingFinal,
                'valor'          => $this->valor,
                'taxa'           => $this->taxa,
                'valor_liquido'  => $this->valor_liquido,
                'status'         => 'payment_pending',
                'payment_status' => 'initiated',
            ]);
        }

        // ── Cobrar via gateway ───────────────────────────────────────────────
        $paymentResult = (new PaymentGateway())->charge([
            'amount'        => $this->valor_total,
            'payment_token' => $this->payment_token,
            'card_name'     => $this->card_name,
            'description'   => 'Pagamento de serviço',
        ]);

        // Zerar campos sensíveis imediatamente após a chamada ao gateway
        $this->card_number = '';
        $this->card_expiry = '';
        $this->card_cvv    = '';

        if (empty($paymentResult['success'])) {
            // Marcar o serviço como falhou
            $service->payment_status = 'failed';
            $service->save();

            // Disparar evento — processado em background pela queue
            PaymentFailed::dispatch(
                $service,
                $user,
                $this->valor_total,
                $paymentResult['message'] ?? 'Falha no processamento do cartão.'
            );

            session()->flash('error', $paymentResult['message'] ?? 'Falha no pagamento. Verifica os dados e tenta novamente.');
            return;
        }

        // ── Pagamento bem-sucedido ───────────────────────────────────────────
        $transactionId = $paymentResult['transaction_id'] ?? null;

        $service->status          = 'published';
        $service->payment_status  = 'paid';
        $service->transaction_id  = $transactionId;
        $service->save();

        session()->forget(['client_order', 'briefing', 'briefing_title']);

        (new AffiliateService())->creditCommissionForReferredAction($user, 'publish_service', $service->id);
        NotifyFreelancersOfNewProject::dispatch($service);

        session()->flash('success', 'Pagamento realizado e pedido publicado com sucesso!');
        return redirect()->route('client.orders');
    }

    public function render()
    {
        return view('livewire.client.payment-escrow')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Pagamento']);
    }
}
