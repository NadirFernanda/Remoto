<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Fees extends Component
{
    // Platform commission rate (%)
    public float  $commissionRate     = 10.0;
    public float  $withdrawFeeFixed   = 2.0;
    public float  $withdrawFeePercent = 1.5;
    public string $savedMsg           = '';

    protected array $rules = [
        'commissionRate'     => 'required|numeric|min:0|max:100',
        'withdrawFeeFixed'   => 'required|numeric|min:0',
        'withdrawFeePercent' => 'required|numeric|min:0|max:100',
    ];

    protected array $messages = [
        'commissionRate.required'     => 'A taxa de comissão é obrigatória.',
        'commissionRate.numeric'      => 'Deve ser um número.',
        'withdrawFeeFixed.required'   => 'O valor fixo de saque é obrigatório.',
        'withdrawFeePercent.required' => 'A percentagem de saque é obrigatória.',
    ];

    public function save(): void
    {
        $this->validate();
        // TODO: persist to settings table
        $this->savedMsg = 'Taxas actualizadas com sucesso.';
    }

    public function render()
    {
        return view('livewire.admin.fees')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Taxas e Comissões']);
    }
}
