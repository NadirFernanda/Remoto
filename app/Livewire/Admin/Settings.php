<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    // ── Saques ────────────────────────────────────────────────────────────────
    public string $withdrawalProcessing      = 'manual';
    public string $withdrawalMinAmount       = '20000';
    public string $withdrawalLiquidityAlert  = '500000';
    public array  $withdrawalMethods         = ['bank_transfer'];

    // ── Notificações & Alertas (Admin) ────────────────────────────────────────
    public array  $adminAlerts         = ['pending_withdrawals_24h','config_risk','change_history','help_tooltips'];
    public array  $adminAlertChannels  = ['email','system'];

    // ── Notificações (Usuários) ───────────────────────────────────────────────
    public array  $userNotifications   = ['withdrawal_processed','value_retained','dispute','fee_change_notice'];

    // ── Relatórios & Exportações ──────────────────────────────────────────────
    public string $reportWithdrawalDaily   = '1';
    public string $reportCommissionMonthly = '1';
    public string $reportTax               = '0';
    public string $reportEmail             = 'contabilidade@24horas.ao';
    public array  $reportFormats           = ['csv','excel','pdf'];

    // ── Dashboard Personalizado ───────────────────────────────────────────────
    public array  $dashboardWidgets = ['top_freelancers','top_creators','top_products','withdrawal_heatmap'];

    public string $savedMsg = '';
    public string $errorMsg = '';

    // ── Perfil pessoal do admin ───────────────────────────────────────────────
    public string $profileName            = '';
    public string $profileEmail           = '';
    public string $profilePassword        = '';
    public string $profilePasswordConfirm = '';
    public string $profileMsg             = '';
    public string $profileMsgType         = 'success';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);

        $this->profileName  = auth()->user()->name;
        $this->profileEmail = auth()->user()->email;

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

        $adminAlerts = PlatformSetting::get('admin_alerts', '["pending_withdrawals_24h","config_risk","change_history","help_tooltips"]');
        $this->adminAlerts        = json_decode($adminAlerts, true) ?? [];
        $adminCh = PlatformSetting::get('admin_alert_channels', '["email","system"]');
        $this->adminAlertChannels = json_decode($adminCh, true) ?? [];

        $userN = PlatformSetting::get('user_notifications', '["withdrawal_processed","value_retained","dispute","fee_change_notice"]');
        $this->userNotifications  = json_decode($userN, true) ?? [];

        $this->reportWithdrawalDaily   = PlatformSetting::get('report_withdrawal_daily',   '1');
        $this->reportCommissionMonthly = PlatformSetting::get('report_commission_monthly',  '1');
        $this->reportTax               = PlatformSetting::get('report_tax',                '0');
        $this->reportEmail             = PlatformSetting::get('report_email',               'contabilidade@24horas.ao');
        $rf = PlatformSetting::get('report_formats', '["csv","excel","pdf"]');
        $this->reportFormats           = json_decode($rf, true) ?? [];

        $dw = PlatformSetting::get('dashboard_widgets', '["top_freelancers","top_creators","top_products","withdrawal_heatmap"]');
        $this->dashboardWidgets        = json_decode($dw, true) ?? [];
    }

    public function saveProfile(): void
    {
        $this->profileMsg = '';
        $user = auth()->user();

        $this->profileName  = trim($this->profileName);
        $this->profileEmail = trim($this->profileEmail);

        $this->validate([
            'profileName'            => 'required|string|min:2|max:100',
            'profileEmail'           => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'profilePassword'        => 'nullable|string|min:10|same:profilePasswordConfirm',
        ], [
            'profileName.required'   => 'O nome é obrigatório.',
            'profileName.min'        => 'O nome deve ter pelo menos 2 caracteres.',
            'profileEmail.required'  => 'O e-mail é obrigatório.',
            'profileEmail.unique'    => 'Este e-mail já está em uso.',
            'profilePassword.min'    => 'A senha deve ter pelo menos 10 caracteres.',
            'profilePassword.same'   => 'As senhas não coincidem.',
        ]);

        $user->name  = $this->profileName;
        $user->email = $this->profileEmail;
        if ($this->profilePassword) {
            $user->password = Hash::make($this->profilePassword);
        }
        $user->save();

        // Re-autenticar para a sessão reflectir o novo nome imediatamente
        \Illuminate\Support\Facades\Auth::setUser($user->fresh());

        $this->profilePassword        = '';
        $this->profilePasswordConfirm = '';
        $this->profileMsg             = 'Perfil actualizado com sucesso.';
        $this->profileMsgType         = 'success';
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
            'adminAlerts'                  => 'nullable|array',
            'adminAlerts.*'               => 'in:pending_withdrawals_24h,suspicious_withdrawal,config_risk,change_history,help_tooltips',
            'adminAlertChannels'           => 'required|array|min:1',
            'adminAlertChannels.*'         => 'in:email,sms,system',
            'userNotifications'            => 'nullable|array',
            'userNotifications.*'          => 'in:withdrawal_processed,value_retained,dispute,weekly_earnings,fee_change_notice',
            'reportWithdrawalDaily'        => 'in:0,1',
            'reportCommissionMonthly'      => 'in:0,1',
            'reportTax'                   => 'in:0,1',
            'reportEmail'                  => 'nullable|email|max:150',
            'reportFormats'                => 'nullable|array',
            'reportFormats.*'              => 'in:csv,excel,pdf',
            'dashboardWidgets'             => 'nullable|array',
            'dashboardWidgets.*'           => 'in:top_freelancers,top_creators,top_products,withdrawal_heatmap',
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
        PlatformSetting::set('admin_alerts',               json_encode($this->adminAlerts ?? []));
        PlatformSetting::set('admin_alert_channels',       json_encode($this->adminAlertChannels));
        PlatformSetting::set('user_notifications',         json_encode($this->userNotifications ?? []));
        PlatformSetting::set('report_withdrawal_daily',    $this->reportWithdrawalDaily);
        PlatformSetting::set('report_commission_monthly',  $this->reportCommissionMonthly);
        PlatformSetting::set('report_tax',                 $this->reportTax);
        PlatformSetting::set('report_email',               $this->reportEmail ?? '');
        PlatformSetting::set('report_formats',             json_encode($this->reportFormats ?? []));
        PlatformSetting::set('dashboard_widgets',          json_encode($this->dashboardWidgets ?? []));

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
