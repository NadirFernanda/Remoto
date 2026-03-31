<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    // ── Plataforma Geral ───────────────────────────────────────────────────────
    public string $siteName        = '';
    public string $siteEmail       = '';
    public string $maintenanceMode = '0';

    // ── Marca & Comunicação ────────────────────────────────────────────────────
    public string $financialSupportEmail = '';
    public string $receiptText           = 'Pagamento processado pela 24Horas Remoto.';
    public string $brandLogoPath         = '';
    public string $walletMinBalance      = '0';
    public mixed  $brandLogo             = null;

    // ── Prazos & Retenção ─────────────────────────────────────────────────────
    public string $freelancerPaymentRelease  = 'immediate'; // immediate | after_confirmation
    public string $creatorPaymentRelease     = 'day_26';    // immediate | day_26
    public string $infoprodutoPaymentRelease = '7_days';    // immediate | 7_days | 14_days

    public string $savedMsg = '';
    public string $errorMsg = '';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);

        $this->siteName        = PlatformSetting::get('site_name', config('app.name', ''));
        $this->siteEmail       = PlatformSetting::get('site_email', config('mail.from.address', ''));
        $this->maintenanceMode = PlatformSetting::get('maintenance_mode', '0');

        $this->financialSupportEmail = PlatformSetting::get('financial_support_email', '');
        $this->receiptText           = PlatformSetting::get('receipt_text', 'Pagamento processado pela 24Horas Remoto.');
        $this->brandLogoPath         = PlatformSetting::get('brand_logo_path', '');
        $this->walletMinBalance      = PlatformSetting::get('wallet_min_balance', '0');

        $this->freelancerPaymentRelease  = PlatformSetting::get('freelancer_payment_release',  'immediate');
        $this->creatorPaymentRelease     = PlatformSetting::get('creator_payment_release',     'day_26');
        $this->infoprodutoPaymentRelease = PlatformSetting::get('infoproduto_payment_release', '7_days');

        $this->withdrawalProcessing     = PlatformSetting::get('withdrawal_processing',      'manual');
        $this->withdrawalMinAmount      = PlatformSetting::get('withdrawal_min_amount',      '20000');
        $this->withdrawalLiquidityAlert = PlatformSetting::get('withdrawal_liquidity_alert', '500000');
        $methods = PlatformSetting::get('withdrawal_methods', '["bank_transfer"]');
        $this->withdrawalMethods        = json_decode($methods, true) ?? ['bank_transfer'];
    }

    public function save(): void
    {
        $this->savedMsg = '';
        $this->errorMsg = '';

        $this->validate([
            'siteName'             => 'required|string|max:100',
            'siteEmail'            => 'required|email|max:150',
            'maintenanceMode'      => 'required|in:0,1',
            'financialSupportEmail'=> 'nullable|email|max:150',
            'receiptText'          => 'nullable|string|max:500',
            'walletMinBalance'             => 'nullable|numeric|min:0',
            'brandLogo'                    => 'nullable|image|max:2048',
            'freelancerPaymentRelease'     => 'required|in:immediate,after_confirmation',
            'creatorPaymentRelease'        => 'required|in:immediate,day_26',
            'infoprodutoPaymentRelease'    => 'required|in:immediate,7_days,14_days',
            'withdrawalProcessing'         => 'required|in:automatic,manual',
            'withdrawalMinAmount'          => 'required|in:0,20000,60000',
            'withdrawalLiquidityAlert'     => 'required|in:500000,1000000',
            'withdrawalMethods'            => 'required|array|min:1',
            'withdrawalMethods.*'          => 'in:bank_transfer,visa,other',
        ]);

        PlatformSetting::set('site_name',        $this->siteName);
        PlatformSetting::set('site_email',        $this->siteEmail);
        PlatformSetting::set('maintenance_mode',  $this->maintenanceMode);
        PlatformSetting::set('financial_support_email', $this->financialSupportEmail ?? '');
        PlatformSetting::set('receipt_text',      $this->receiptText ?? '');
        PlatformSetting::set('wallet_min_balance', $this->walletMinBalance ?? '0');
        PlatformSetting::set('freelancer_payment_release',  $this->freelancerPaymentRelease);
        PlatformSetting::set('creator_payment_release',     $this->creatorPaymentRelease);
        PlatformSetting::set('infoproduto_payment_release', $this->infoprodutoPaymentRelease);
        PlatformSetting::set('withdrawal_processing',      $this->withdrawalProcessing);
        PlatformSetting::set('withdrawal_min_amount',      $this->withdrawalMinAmount);
        PlatformSetting::set('withdrawal_liquidity_alert', $this->withdrawalLiquidityAlert);
        PlatformSetting::set('withdrawal_methods',         json_encode($this->withdrawalMethods));

        // Logo upload
        if ($this->brandLogo) {
            $ext  = $this->brandLogo->getClientOriginalExtension();
            $path = $this->brandLogo->storePubliclyAs('brand', 'logo.' . $ext, 'public');
            PlatformSetting::set('brand_logo_path', $path);
            $this->brandLogoPath = $path;
            $this->brandLogo     = null;
        }

        // Activar/desactivar modo de manutenção
        if ($this->maintenanceMode === '1') {
            if (!file_exists(base_path('storage/framework/down'))) {
                \Artisan::call('down');
            }
        } else {
            if (file_exists(base_path('storage/framework/down'))) {
                \Artisan::call('up');
            }
        }

        $this->savedMsg = 'Configurações guardadas com sucesso.';
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Configurações do Sistema']);
    }
}
