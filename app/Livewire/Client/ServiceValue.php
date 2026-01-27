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
        $this->valor_liquido = $this->valor - ($this->valor * $this->taxa / 100);
    }


    public function updatedValor($value)
    {
        $this->valor = (float)$value;
        $this->valor_liquido = $this->valor - ($this->valor * $this->taxa / 100);
    }


    public function submitValue()
    {
        $this->validate([
            'valor' => 'required|numeric|min:10000',
        ], [
            'valor.min' => 'O valor do serviço deve ser no mínimo 10.000 Kz.',
        ]);
        // Salvar dados de pagamento na sessão
        session([
            'pagamento' => [
                'valor' => $this->valor,
                'taxa' => $this->taxa,
                'valor_liquido' => $this->valor_liquido,
            ]
        ]);
        return redirect()->route('client.payment', ['valor' => $this->valor]);
    }

    public function render()
    {
        return view('livewire.client.service-value');
    }
}
