<?php

namespace App\Livewire\Client;

use App\Events\PaymentFailed;
use App\Jobs\InitiateAppyPayChargeJob;
use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\PlatformSetting;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use App\Modules\Payments\Services\PaymentGateway;
use App\Services\AffiliateService;
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

    public string $payment_method = 'card';

    public $listeners = ['updatedPaymentMethod' => 'render'];

    // Campos de UI apenas — nunca persistidos. Em produção o gateway front-end
    // gera um token opaco e escreve em $payment_token via Livewire.
    public string $card_name    = '';
    public string $card_number  = '';
    public string $card_expiry  = '';
    public string $card_cvv     = '';
    public string $payment_token = '';

    // ── AppyPay (Multicaixa Express / Referência) ─────────────────────────
    public string $phone_number       = '';
    public string $appypay_step       = 'form'; // form | waiting | reference | done
    public ?int   $appypay_service_id = null;
    public ?string $appypay_charge_id = null;
    public ?string $appypay_reference = null;
    public ?string $appypay_entity    = null;
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
            'valor_liquido'  => $this->valor_liquido,
            'status'         => 'payment_pending',
            'payment_status' => 'initiated',
        ]);
    }

    public function confirmPayment()
    {
        if (in_array($this->payment_method, ['express', 'bank'])) {
            // Tratados pelos seus próprios métodos (chargeAppyPayPhone/chargeAppyPayReference).
            return;
        }

        // ── Autenticação ─────────────────────────────────────────────────────
        $user = $this->getCurrentUser();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        if ($this->valorAbaixoDoMinimo()) {
            return;
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
        $service = $this->resolveOrCreateService($user);
        if (!$service) {
            session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
            return redirect()->route('client.briefing');
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

        $this->registarEntradaEmEscrow($service, $user->id);

        session()->forget(['client_order', 'briefing', 'briefing_title']);

        (new AffiliateService())->creditCommissionForReferredAction($user, 'publish_service', $service->id);
        NotifyFreelancersOfNewProject::dispatch($service);

        session()->flash('success', 'Pagamento realizado e pedido publicado com sucesso!');
        return redirect()->route('client.orders');
    }

    /** A AppyPay exige merchantTransactionId só alfanumérico, entre 1 e 15 caracteres. */
    private function generateMerchantTransactionId(): string
    {
        return strtoupper(Str::random(12));
    }

    /**
     * Regista a entrada em escrow no momento em que o pagamento é confirmado
     * — antes disto, um projecto pago (status='published') não deixava
     * nenhum rasto em WalletLog, por isso ficava invisível para "Total
     * Entradas" no Painel Financeiro/Fluxo de Caixa até (e só até) um
     * freelancer ser escolhido (ver ProjectManager::escolherFreelancer).
     *
     * Só o registo — não mexe em saldo/saldo_pendente, porque este projecto
     * foi pago externamente (cartão/AppyPay), não a partir de saldo já
     * existente na carteira. A mecânica de saldo/saldo_pendente ao escolher
     * freelancer (e a sua reversão em ServiceCancel/Client\Dashboard) fica
     * exactamente como estava — só deixa de criar este MESMO registo outra
     * vez nesse momento, para não contar a entrada em duplicado.
     */
    private function registarEntradaEmEscrow(Service $service, int $clienteId): void
    {
        if (!$service->valor || $service->valor <= 0) {
            return;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $clienteId],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
        );

        WalletLog::create([
            'user_id'   => $clienteId,
            'wallet_id' => $wallet->id,
            'valor'     => -(float) $service->valor,
            'tipo'      => 'escrow_retido',
            'descricao' => 'Pagamento retido em escrow para o projecto: ' . $service->titulo,
        ]);
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
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
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

    // ── AppyPay: Referência de pagamento ───────────────────────────────────

    public function chargeAppyPayReference()
    {
        $this->appypay_error = '';

        if ($this->valorAbaixoDoMinimo()) {
            return;
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        $service = $this->resolveOrCreateService($user);
        if (!$service) {
            session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
            return redirect()->route('client.briefing');
        }

        $merchantTransactionId = $this->generateMerchantTransactionId();
        $service->appypay_merchant_transaction_id = $merchantTransactionId;
        $service->save();

        $result = (new AppyPayGateway())->chargeByReference(
            $this->valor_total,
            'Pagamento de serviço #' . $service->id,
            $merchantTransactionId
        );

        if (empty($result['success'])) {
            $this->appypay_error = $result['message'] ?? 'Não foi possível gerar a referência. Tente novamente.';
            \App\Modules\Admin\Services\AuditLogger::log(
                'appypay_charge_ambiguous',
                "Pedido de referência AppyPay falhou/expirou para o serviço #{$service->id} (merchantTransactionId: {$merchantTransactionId}) — estado do pagamento do lado da AppyPay não confirmado, requer verificação manual.",
                'Service',
                $service->id
            );
            return;
        }

        $service->payment_method_used = 'appypay_ref';
        $service->appypay_charge_id   = $result['charge_id'];
        $service->payment_reference   = $result['reference'];
        $service->payment_entity      = $result['entity'];
        $service->save();

        PollAppyPayChargeJob::dispatch($service, $result['charge_id'], 'ref')->delay(now()->addMinutes(5));

        $this->appypay_service_id = $service->id;
        $this->appypay_charge_id  = $result['charge_id'];
        $this->appypay_reference  = $result['reference'];
        $this->appypay_entity     = $result['entity'];
        $this->appypay_step       = 'reference';

        session()->forget(['client_order', 'briefing', 'briefing_title']);
    }

    /** Apenas sandbox — simula o pagamento da referência gerada, para testar o fluxo de ponta a ponta. */
    public function mockConfirmAppyPayReference()
    {
        if (config('services.appypay.mode') !== 'sandbox' || !$this->appypay_reference) {
            return;
        }

        (new AppyPayGateway())->mockReferencePayment($this->appypay_reference);
        $this->checkAppyPayStatus();
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
            session()->flash('success', 'Pagamento confirmado e pedido publicado com sucesso!');
            return redirect()->route('client.orders');
        }

        if ($service->payment_status === 'failed') {
            $this->appypay_error = 'O pagamento não foi confirmado. Tente novamente ou escolha outro método.';
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
            session()->flash('success', 'Pagamento confirmado e pedido publicado com sucesso!');
            return redirect()->route('client.orders');
        }
    }

    public function render()
    {
        return view('livewire.client.payment-escrow')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Pagamento']);
    }
}
