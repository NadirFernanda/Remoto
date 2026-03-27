<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PlatformSetting;
use App\Modules\Admin\Services\AuditLogger;

class Fees extends Component
{
    // ── Projetos / Serviços ──────────────────────────────────────────
    public float $serviceClientFeeRate     = 10.0;  // % adicionado ao cliente
    public float $serviceFreelancerFeeRate = 20.0;  // % retido ao freelancer

    // ── Loja (Infoprodutos) ──────────────────────────────────────────
    public float $lojaFeeRate = 20.0;               // % comissão plataforma

    // ── Assinaturas de Criadores ─────────────────────────────────────
    public float $subscriptionFeeRate = 25.0;       // % comissão plataforma

    // ── Patrocínio de Infoprodutos ───────────────────────────────────
    public float $patrocinioDiario = 600.0;         // AOA por dia

    // ── Programa de Afiliados ────────────────────────────────────────
    public float $affiliateSignupCommission = 200.0; // AOA por registo indicado

    // ── Saques ───────────────────────────────────────────────────────
    public float $withdrawFeeFixed   = 2.0;
    public float $withdrawFeePercent = 1.5;

    public string $savedMsg  = '';
    public string $errorMsg  = '';

    protected array $rules = [
        'serviceClientFeeRate'     => 'required|numeric|min:0|max:100',
        'serviceFreelancerFeeRate' => 'required|numeric|min:0|max:100',
        'lojaFeeRate'              => 'required|numeric|min:0|max:100',
        'subscriptionFeeRate'      => 'required|numeric|min:0|max:100',
        'patrocinioDiario'         => 'required|numeric|min:0',
        'affiliateSignupCommission'=> 'required|numeric|min:0',
        'withdrawFeeFixed'         => 'required|numeric|min:0',
        'withdrawFeePercent'       => 'required|numeric|min:0|max:100',
    ];

    protected array $messages = [
        'serviceClientFeeRate.required'      => 'Campo obrigatório.',
        'serviceFreelancerFeeRate.required'  => 'Campo obrigatório.',
        'lojaFeeRate.required'               => 'Campo obrigatório.',
        'subscriptionFeeRate.required'       => 'Campo obrigatório.',
        'patrocinioDiario.required'          => 'Campo obrigatório.',
        'affiliateSignupCommission.required' => 'Campo obrigatório.',
        'withdrawFeeFixed.required'          => 'Campo obrigatório.',
        'withdrawFeePercent.required'        => 'Campo obrigatório.',
    ];

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);

        $this->serviceClientFeeRate      = (float) PlatformSetting::get('service_client_fee_rate',      10);
        $this->serviceFreelancerFeeRate  = (float) PlatformSetting::get('service_freelancer_fee_rate',  20);
        $this->lojaFeeRate               = (float) PlatformSetting::get('loja_fee_rate',                20);
        $this->subscriptionFeeRate       = (float) PlatformSetting::get('subscription_fee_rate',        25);
        $this->patrocinioDiario          = (float) PlatformSetting::get('patrocinio_diario',            600);
        $this->affiliateSignupCommission = (float) PlatformSetting::get('affiliate_signup_commission',  200);
        $this->withdrawFeeFixed          = (float) PlatformSetting::get('withdraw_fee_fixed',           2);
        $this->withdrawFeePercent        = (float) PlatformSetting::get('withdraw_fee_percent',         1.5);
    }

    public function save(): void
    {
        $this->savedMsg = '';
        $this->errorMsg = '';
        $this->validate();

        PlatformSetting::set('service_client_fee_rate',      $this->serviceClientFeeRate);
        PlatformSetting::set('service_freelancer_fee_rate',  $this->serviceFreelancerFeeRate);
        PlatformSetting::set('loja_fee_rate',                $this->lojaFeeRate);
        PlatformSetting::set('subscription_fee_rate',        $this->subscriptionFeeRate);
        PlatformSetting::set('patrocinio_diario',            $this->patrocinioDiario);
        PlatformSetting::set('affiliate_signup_commission',  $this->affiliateSignupCommission);
        PlatformSetting::set('withdraw_fee_fixed',           $this->withdrawFeeFixed);
        PlatformSetting::set('withdraw_fee_percent',         $this->withdrawFeePercent);

        AuditLogger::log('fees_updated', 'Taxas da plataforma actualizadas pelo administrador.');

        $this->savedMsg = 'Taxas actualizadas com sucesso!';
    }

    public function render()
    {
        return view('livewire.admin.fees')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Taxas e Comissões']);
    }
}

