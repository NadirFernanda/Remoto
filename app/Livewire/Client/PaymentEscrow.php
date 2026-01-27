<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class PaymentEscrow extends Component
{
    public float $valor = 10000;
    public float $taxa = 10.0;
    public float $valor_liquido = 0;

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
    public function processBriefing(array $briefing): array
    {
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
        $pagamento = session('pagamento', null);
        if ($pagamento) {
            $this->valor = (float)($pagamento['valor'] ?? 10000);
            $this->taxa = (float)($pagamento['taxa'] ?? 10.0);
            $this->valor_liquido = (float)($pagamento['valor_liquido'] ?? ($this->valor - ($this->valor * $this->taxa / 100)));
        } else {
            $valor = request()->query('valor', 10000);
            $this->valor = (float)$valor;
            $this->valor_liquido = $this->valor - ($this->valor * $this->taxa / 100);
        }
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
        } elseif ($this->payment_method === 'paypal') {
            // Simulação: redireciona para rota fake de PayPal, passando briefing e pagamento na query string
            $briefing = session('briefing', []);
            $pagamento = [
                'valor' => $this->valor,
                'taxa' => $this->taxa,
                'valor_liquido' => $this->valor_liquido,
            ];
            return redirect()->route('client.paypal', [
                'briefing' => json_encode($briefing),
                'pagamento' => json_encode($pagamento)
            ]);
        } elseif ($this->payment_method === 'express') {
            // Simulação: lógica para Express
            // return redirect()->route('client.express');
            return; // Simulação: não faz nada
        } elseif ($this->payment_method === 'bank') {
            // Simulação: lógica para transferência bancária
            // return redirect()->route('client.bank');
            return; // Simulação: não faz nada
        }

        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'É necessário estar autenticado para publicar um pedido.');
            return redirect()->route('client.payment', ['valor' => $this->valor]);
        }

        // Recupera dados do briefing da sessão (ajuste conforme seu fluxo real)
        $briefing = session('briefing', null);
        if (!$briefing) {
            session()->flash('error', 'Preencha o briefing antes de prosseguir com o pagamento.');
            return redirect()->route('client.briefing');
        }

        // Aqui seria feita a integração real com gateway de pagamento
        // Simulação de sucesso

        // Processa o briefing antes de salvar
        $briefing_processado = $this->processBriefing($briefing);
        $service = Service::create([
            'cliente_id' => $user->id,
            'titulo' => $briefing_processado['business_type'] ?? 'Pedido sem título',
            'briefing' => json_encode($briefing_processado),
            'valor' => $this->valor,
            'taxa' => $this->taxa,
            'valor_liquido' => $this->valor_liquido,
            'status' => 'aguardando',
        ]);

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
        return view('livewire.client.payment-escrow');
    }
}
