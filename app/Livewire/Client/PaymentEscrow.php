<?php

namespace App\Livewire\Client;

use App\Jobs\InitiateAppyPayChargeJob;
use App\Models\PlatformSetting;
use App\Models\Service;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use App\Services\FeeService;
use App\Traits\UserSessionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentEscrow extends Component
{
    use UserSessionTrait;

    public float $valor        = 5;
    public float $taxa_cliente = 0.0;
    public float $valor_total  = 0.0;
    public float $taxa         = 0.0;
    public float $valor_liquido = 0;

    // ── AppyPay: Multicaixa Express (único método de pagamento) ───────────
    public string $phone_number       = '';
    public string $appypay_step       = 'form'; // form | waiting | done
    public ?int   $appypay_service_id = null;
    public ?string $appypay_charge_id = null;
    public string $appypay_error      = '';

    public function mount(): void
    {
        $order    = session('client_order', []);
        $pagamento = $order['payment'] ?? session('pagamento', null);
        $valorMinimo = (float) PlatformSetting::get('project_min_value', 5);

        $this->valor = $pagamento
            ? (float)($pagamento['valor'] ?? $valorMinimo)
            : (float)request()->query('valor', $valorMinimo);

        $fee = (new FeeService())->calculateServiceFee($this->valor);
        $this->taxa_cliente  = $fee['taxa_cliente'];
        $this->valor_total   = $fee['total_cliente'];
        $this->taxa          = $fee['taxa'];
        $this->valor_liquido = $fee['valor_liquido'];
    }

    /**
     * Recupera o serviço em curso (da sessão) ou cria um novo, no estado
     * payment_pending/initiated — partilhado por todos os métodos de pagamento.
     * Devolve null (com mensagem/redirect já tratados pelo chamador) se faltar
     * informação do briefing.
     */
    private function resolveOrCreateService($user): ?Service
    {
        $order     = session('client_order', []);
        $serviceId = $order['service_id'] ?? null;
        $service   = $serviceId
            ? Service::where('id', $serviceId)->where('cliente_id', $user->id)->first()
            : null;

        if ($service) {
            $service->valor          = $this->valor;
            $service->taxa           = $this->taxa;
            $service->taxa_cliente   = $this->taxa_cliente;
            $service->total_cliente  = $this->valor_total;
            $service->valor_liquido  = $this->valor_liquido;
            $service->payment_status = 'initiated';
            $service->status         = 'payment_pending';
            $service->save();

            return $service;
        }

        $briefing = $order['briefing_raw'] ?? session('briefing', null);
        $titulo   = $order['title'] ?? session('briefing_title');

        if (!$briefing || !$titulo) {
            return null;
        }

        $briefingFinal = is_array($briefing)
            ? ($briefing['texto'] ?? json_encode($briefing))
            : (string)$briefing;

        return Service::create([
            'cliente_id'     => $user->id,
            'titulo'         => is_string($titulo) ? trim($titulo) : '',
            'briefing'       => $briefingFinal,
            'valor'          => $this->valor,
            'taxa'           => $this->taxa,
            'taxa_cliente'   => $this->taxa_cliente,
            'total_cliente'  => $this->valor_total,
            'valor_liquido'  => $this->valor_liquido,
            'status'         => 'payment_pending',
            'payment_status' => 'initiated',
        ]);
    }

    /** A AppyPay exige merchantTransactionId só alfanumérico, entre 1 e 15 caracteres. */
    private function generateMerchantTransactionId(): string
    {
        return strtoupper(Str::random(12));
    }

    /**
     * Última barreira antes de qualquer cobrança real — alguns rascunhos
     * antigos (de tentativas de pagamento falhadas) ficaram gravados com
     * valor 0, e sem isto o cliente conseguia chegar a "Pagar 0 Kz via
     * Express" e a AppyPay simplesmente rejeitava o pedido com um erro
     * genérico. Nunca deixar tentar cobrar abaixo do mínimo configurado.
     */
    private function valorAbaixoDoMinimo(): bool
    {
        $minimo = (float) PlatformSetting::get('project_min_value', 5);

        if ($this->valor < $minimo) {
            $this->appypay_error = 'O valor deste projecto é inválido (' . number_format($this->valor, 0, ',', '.') . ' Kz). Volte ao passo de investimento e defina um valor de pelo menos ' . number_format($minimo, 0, ',', '.') . ' Kz antes de pagar.';
            return true;
        }

        return false;
    }

    // ── AppyPay: Multicaixa Express (telefone) ────────────────────────────

    public function chargeAppyPayPhone()
    {
        $this->appypay_error = '';

        if ($this->valorAbaixoDoMinimo()) {
            return;
        }

        $this->validate([
            'phone_number' => ['required', 'regex:/^9[0-9]{8}$/'],
        ], [
            'phone_number.required' => 'Indique o número de telefone Multicaixa Express.',
            'phone_number.regex'    => 'Número inválido — use 9 dígitos (ex: 923456789).',
        ]);

        $user = $this->getCurrentUser();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um projecto.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        $service = $this->resolveOrCreateService($user);
        if (!$service) {
            session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
            return redirect()->route('client.briefing');
        }

        // Gravado ANTES da chamada — se o pedido expirar do nosso lado sem
        // recebermos o charge_id da AppyPay, ainda ficamos com este ID para
        // reconciliar manualmente com o suporte deles, em vez de perder por
        // completo o rasto de uma cobrança que possa ter sido processada do
        // lado deles apesar do timeout.
        $merchantTransactionId = $this->generateMerchantTransactionId();
        $service->appypay_merchant_transaction_id = $merchantTransactionId;
        $service->save();

        // A chamada à AppyPay corre em segundo plano (não aqui, de forma
        // síncrona) — o cliente sai da nossa página para aprovar no
        // telemóvel, o que pode por si só demorar mais do que qualquer
        // timeout razoável para um pedido web. Ver InitiateAppyPayChargeJob.
        InitiateAppyPayChargeJob::dispatch($service, 'gpo', $this->phone_number, $this->valor_total, $merchantTransactionId);

        $this->appypay_service_id = $service->id;
        $this->appypay_step       = 'waiting';

        session()->forget(['client_order', 'briefing', 'briefing_title']);
    }

    /**
     * Chamado via wire:poll no ecrã de espera (a cada 3s) — verifica o estado
     * no NOSSO serviço primeiro, nunca à espera de resposta imediata da
     * AppyPay. Para o método GPO (telefone), InitiateAppyPayChargeJob pode
     * ainda estar a criar a cobrança em segundo plano quando este poll
     * corre — nesse caso ainda não há charge_id, e este método só volta a
     * verificar no próximo ciclo, sem bloquear nem mostrar erro.
     */
    public function checkAppyPayStatus()
    {
        if (!$this->appypay_service_id) {
            return;
        }

        $service = Service::find($this->appypay_service_id);
        if (!$service) {
            return;
        }

        if ($service->payment_status === 'paid') {
            $this->appypay_step = 'done';
            session()->flash('success', 'Pagamento confirmado e projecto publicado com sucesso!');
            return redirect()->route('client.orders');
        }

        if ($service->payment_status === 'failed') {
            $this->appypay_error = 'O pagamento não foi confirmado. Tente novamente.';
            $this->appypay_step  = 'form';
            return;
        }

        // O job em segundo plano ainda pode não ter conseguido o charge_id
        // (chamada à AppyPay ainda em curso, ou pendente na fila) — sem ele
        // não há nada para consultar ainda; o próximo ciclo de 3s tenta de novo.
        $chargeId = $this->appypay_charge_id ?: $service->appypay_charge_id;
        if (!$chargeId) {
            return;
        }
        $this->appypay_charge_id = $chargeId;

        // Consulta directa (não depende só do job em fila) — mais responsivo para o utilizador à espera.
        $charge = (new AppyPayGateway())->getCharge($chargeId);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(AppyPayReconciliationService::class)->markPaidByChargeId($chargeId);
            $this->appypay_step = 'done';
            session()->flash('success', 'Pagamento confirmado e projecto publicado com sucesso!');
            return redirect()->route('client.orders');
        }
    }

    public function render()
    {
        return view('livewire.client.payment-escrow')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Pagamento']);
    }
}
