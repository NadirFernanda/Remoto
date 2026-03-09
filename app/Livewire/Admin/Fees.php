<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PlatformSetting;

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

    public function mount(): void
    {
        $this->commissionRate     = (float) PlatformSetting::get('commission_rate', 10);
        $this->withdrawFeeFixed   = (float) PlatformSetting::get('withdraw_fee_fixed', 2);
        $this->withdrawFeePercent = (float) PlatformSetting::get('withdraw_fee_percent', 1.5);
    }

    public function save(): void
    {
        $this->validate();
        PlatformSetting::set('commission_rate',      $this->commissionRate);
        PlatformSetting::set('withdraw_fee_fixed',   $this->withdrawFeeFixed);
        PlatformSetting::set('withdraw_fee_percent', $this->withdrawFeePercent);
        $this->savedMsg = 'Taxas actualizadas com sucesso.';
    }

    public function render()
    {
        return view('livewire.admin.fees')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Taxas e Comissões']);
    }
}

