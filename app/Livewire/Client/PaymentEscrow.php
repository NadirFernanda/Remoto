<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;
use App\Services\FeeService;
use App\Modules\Marketplace\Services\FreelancerService;
use App\Modules\Payments\Services\PaymentGateway;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;

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

    // Campos de pagamento cartão
    public $card_name = '';
    public $card_number = '';
    public $card_expiry = '';
    public $card_cvv = '';

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
            $this->validate([
                'card_name' => 'required|string|min:3',
                'card_number' => 'required|digits_between:13,19',
                'card_expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/(\d{2})$/'],
                'card_cvv' => 'required|digits_between:3,4',
            ], [
                'card_name.required' => 'Informe o nome do titular do cartão.',
                'card_number.required' => 'Informe o número do cartão.',
                'card_expiry.required' => 'Informe a validade.',
                'card_cvv.required' => 'Informe o CVV.',
                'card_number.digits_between' => 'Número do cartão inválido.',
                'card_expiry.regex' => 'Validade deve ser no formato MM/AA.',
                'card_cvv.digits_between' => 'CVV inválido.',
            ]);
            // Integração com gateway de pagamento
            $paymentResult = PaymentGateway::charge([
                'amount'      => $this->valor_total,  // cobra o total real (valor + taxa)
                'card_name'   => $this->card_name,
                'card_number' => $this->card_number,
                'card_expiry' => $this->card_expiry,
                'card_cvv' => $this->card_cvv,
                'description' => 'Pagamento de serviço',
            ]);
            if (empty($paymentResult['success'])) {
                session()->flash('error', $paymentResult['message'] ?? 'Falha no pagamento.');
                return;
            }
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

        // Notificação interna para freelancers ativos
        if ($service) {
            $freelancers = FreelancerService::getAllFreelancers();
            foreach ($freelancers as $freelancer) {
                Notification::create([
                    'user_id' => $freelancer->id,
                    'type' => 'novo_projeto',
                    'title' => 'Novo projeto publicado',
                    'message' => 'Um novo projeto foi publicado: ' . $service->titulo,
                    'read' => false,
                ]);
                // Notificação por e-mail (apenas se preferir)
                if ($freelancer->notify_new_project_email) {
                    $serviceUrl = route('freelancer.service.review', $service->id);
                    $freelancer->notify(new \App\Notifications\NewProjectNotification($service, $serviceUrl));
                }
            }
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
        // DEBUG: Exibir valor recebido na URL
        session()->flash('debug_valor', 'Valor recebido: ' . $this->valor);
        return view('livewire.client.payment-escrow')
            ->layout('layouts.livewire');
    }
}
