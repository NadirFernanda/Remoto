<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;
use App\Services\FeeService;
use App\Modules\Payments\Services\PaymentGateway;
use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\Service;
use App\Models\User;

class PaymentEscrow extends Component
{
    use UserSessionTrait;
    public float $valor = 10000;
    public float $taxa_cliente = 0.0;   // 10% charged to client (on top of project price)
    public float $valor_total = 0.0;    // total the client actually pays
    public float $taxa = 0.0;           // 20% deducted from freelancer
    public float $valor_liquido = 0;    // what the freelancer receives

    // Método de pagamento
    public $payment_method = 'card'; // 'card', 'paypal', 'express', 'bank'
        
    public $listeners = ['updatedPaymentMethod' => 'render'];

    // Campos de pagamento cartão.
    // SEGURANÇA: nunca usados para armazenar dados reais — servem apenas
    // como campos temporários de UI para validação estrutural local.
    // Em integração real, substituir por token emitido pelo SDK do gateway
    // (ex: Stripe.js, Multicaixa SDK) — nunca enviar PAN/CVV ao servidor.
    public string $card_name   = '';
    public string $card_number = '';
    public string $card_expiry = '';
    public string $card_cvv    = '';
    public string $payment_token = ''; // token gerado pelo gateway front-end (produção)

    /**
     * Processa o briefing do cliente: corrige ortografia, remove ofensas e clarifica o texto.
     * (Simples exemplo, pode ser expandido com IA ou APIs externas)
     */
    public function processBriefing($briefing): array
    {
        // Se já for string (texto profissional), retorna como array padronizado
        if (is_string($briefing)) {
            return ['texto' => $briefing];
        }
        $ofensas = ['idiota', 'burro', 'estúpido']; // Exemplo de palavras ofensivas
        $substituir = '[removido]';
        $processado = [];
        foreach ($briefing as $campo => $texto) {
            // Corrigir erros comuns (exemplo simples)
            $texto = str_replace(['logotípo', 'logotipoo'], 'logotipo', $texto);
            // Remover ofensas
            $texto = str_ireplace($ofensas, $substituir, $texto);
            // Clarificar: remover excesso de espaços, capitalizar primeira letra
            $texto = ucfirst(trim(preg_replace('/\s+/', ' ', $texto)));
            $processado[$campo] = $texto;
        }
        return $processado;
    }

    public function mount() {
        $order = session('client_order', []);
        $pagamento = $order['payment'] ?? session('pagamento', null);

        if ($pagamento) {
            $this->valor = (float)($pagamento['valor'] ?? 10000);
        } else {
            $this->valor = (float)request()->query('valor', 10000);
        }

        // Calcula taxas duais: 10% do cliente + 20% do freelancer
        $fee = (new FeeService())->calculateServiceFee($this->valor);
        $this->taxa_cliente  = $fee['taxa_cliente'];
        $this->valor_total   = $fee['total_cliente'];
        $this->taxa          = $fee['taxa'];
        $this->valor_liquido = $fee['valor_liquido'];
    }

    public function confirmPayment()
    {
        // Validação dinâmica conforme método de pagamento
        if ($this->payment_method === 'card') {
            // SEGURANÇA (PCI-DSS): os dados brutos do cartão NUNCA devem ser
            // submetidos ao servidor. O front-end (JavaScript do gateway — ex:
            // Multicaixa Express SDK, Stripe.js) converte o cartão num token
            // opaco e armazena-o em $this->payment_token via Livewire.
            // Dados de cartão (PAN, CVV, expiry) são zerados após tokenização.
            $this->validate([
                'payment_token' => 'required|string|min:8',
                'card_name'     => 'required|string|min:3|max:100',
            ], [
                'payment_token.required' => 'Erro ao processar o cartão. Tente novamente.',
                'card_name.required'     => 'Informe o nome do titular do cartão.',
            ]);

            // Enviar apenas o token ao gateway — NUNCA PAN, CVV ou expiry
            $paymentResult = PaymentGateway::charge([
                'amount'        => $this->valor_total,
                'payment_token' => $this->payment_token,
                'card_name'     => $this->card_name,
                'description'   => 'Pagamento de serviço',
            ]);
            if (empty($paymentResult['success'])) {
                session()->flash('error', $paymentResult['message'] ?? 'Falha no pagamento.');
                return;
            }
            // Zerar dados sensíveis da memória imediatamente após uso
            $this->card_number = '';
            $this->card_expiry = '';
            $this->card_cvv    = '';
            $transactionId = $paymentResult['transaction_id'] ?? null;
        } elseif ($this->payment_method === 'paypal') {
            // SIMULAÇÃO: PayPal aprovado automaticamente em modo de testes
            $transactionId = 'PAYPAL-SIM-' . strtoupper(uniqid());
        } elseif ($this->payment_method === 'express') {
            session()->flash('error', 'Pagamento Express ainda não está disponível. Por favor, escolha outro método.');
            return;
        } elseif ($this->payment_method === 'bank') {
            session()->flash('error', 'Pagamento por transferência bancária ainda não está disponível. Por favor, escolha outro método.');
            return;
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        // Recupera dados da sessão
        $order = session('client_order', []);

        // Tenta encontrar o Service já criado na etapa do briefing
        $serviceId = $order['service_id'] ?? null;
        $service = $serviceId ? Service::where('id', $serviceId)->where('cliente_id', $user->id)->first() : null;

        if ($service) {
            // Atualiza o service existente com o valor e valor líquido
            $service->valor         = $this->valor;
            $service->taxa          = $this->taxa;
            $service->valor_liquido = $this->valor_liquido;
            $service->status        = 'published';
            $service->save();
        } else {
            // Fallback: cria service a partir da sessão (fluxo legado)
            $briefing = $order['briefing_raw'] ?? session('briefing', null);
            if (!$briefing) {
                session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
                return redirect()->route('client.briefing');
            }
            $briefing_processado = $this->processBriefing($briefing);
            $titulo_cliente = $order['title'] ?? session('briefing_title');
            $titulo = is_string($titulo_cliente) && trim($titulo_cliente) !== '' ? trim($titulo_cliente) : ($briefing['title'] ?? null);
            if (!$titulo) {
                session()->flash('error', 'Título do pedido não encontrado. Volte e preencha o título corretamente.');
                return redirect()->route('client.briefing');
            }
            $briefing_final = is_array($briefing_processado)
                ? (isset($briefing_processado['texto']) ? $briefing_processado['texto'] : json_encode($briefing_processado))
                : (string) $briefing_processado;

            $service = Service::create([
                'cliente_id'    => $user->id,
                'titulo'        => $titulo,
                'briefing'      => $briefing_final,
                'valor'         => $this->valor,
                'taxa'          => $this->taxa,
                'valor_liquido' => $this->valor_liquido,
                'status'        => 'published',
            ]);
        }

        session()->forget(['client_order', 'briefing', 'briefing_title']);

        // Despachar notificação em background (queue job) para evitar
        // bloquear o request com N inserts + N emails síncronos.
        if ($service) {
            \App\Jobs\NotifyFreelancersOfNewProject::dispatch($service);
        }

        if ($service) {
            session()->flash('success', 'Pagamento realizado e pedido publicado com sucesso!');
            return redirect()->route('client.orders');
        } else {
            session()->flash('error', 'Erro ao criar o pedido. Tente novamente.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }
    }

    public function render()
    {
        return view('livewire.client.payment-escrow')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Pagamento']);
    }
}
