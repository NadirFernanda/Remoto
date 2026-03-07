<?php

namespace App\Livewire\Client;

use Livewire\Component;

class ServiceValue extends Component
{
    public float $valor = 10000;
    public float $taxa = 10.0; // 10%
    public float $valor_liquido = 0;
    public function mount()
    {
        // Se já houver dados de pedido na sessão, respeita o valor definido anteriormente
        $order = session('client_order', []);
        if (isset($order['payment']['valor'], $order['payment']['taxa'], $order['payment']['valor_liquido'])) {
            $this->valor = (float) $order['payment']['valor'];
            $this->taxa = (float) $order['payment']['taxa'];
            $this->valor_liquido = (float) $order['payment']['valor_liquido'];
        } else {
            $this->valor_liquido = $this->valor - ($this->valor * $this->taxa / 100);
        }
    }


    public function updatedValor($value)
    {
        $this->valor = (float)$value;
        $this->valor_liquido = $this->valor - ($this->valor * $this->taxa / 100);
    }


    public function submitValue()
    {
        // Garante que o briefing foi preenchido antes de definir o valor
        $order = session('client_order', []);
        if (empty($order['briefing_raw']) && empty($order['briefing_text'])) {
            session()->flash('error', 'Preencha o briefing antes de definir o valor do serviço.');
            return redirect()->route('client.briefing');
        }

        $this->validate([
            'valor' => 'required|numeric|min:10000',
        ], [
            'valor.min' => 'O valor do serviço deve ser no mínimo 10.000 Kz.',
        ]);
        // Atualizar objeto único de pedido na sessão
        $order['payment'] = [
            'valor' => $this->valor,
            'taxa' => $this->taxa,
            'valor_liquido' => $this->valor_liquido,
        ];
        session([
            'client_order' => $order,
            // Mantém estrutura antiga para compatibilidade
            'pagamento' => $order['payment'],
        ]);
        return redirect()->route('client.payment', ['service' => session('client_order.service_id', 0), 'valor' => $this->valor]);
    }

    public function render()
    {
        return view('livewire.client.service-value');
    }
}
