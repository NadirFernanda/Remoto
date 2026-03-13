<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Controllers\ContractController;
use App\Modules\Admin\Services\ExchangeRateService;

// ─── Admin Module Routes ──────────────────────────────────────────────────────

Route::middleware(['web', 'auth', 'admin.module:gestor'])->group(function () {
    // Exchange rate refresh API endpoint
    Route::post('/refresh-aoa-rate', function (ExchangeRateService $svc) {
        $rate = $svc->refresh();
        return response()->json(['status' => 'ok', 'rate' => $rate]);
    });

    // Commercial contracts management
    Route::resource('comercial', ContractController::class)->names('admin.comercial');

    // Admin dashboards
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', \App\Livewire\Admin\Users::class)->name('admin.users');
    Route::get('/admin/services', \App\Livewire\Admin\Services::class)->name('admin.services');
    Route::get('/admin/disputas', \App\Livewire\Admin\DisputeAdmin::class)->name('admin.disputes');
    Route::get('/admin/auditoria', \App\Livewire\Admin\AuditLogs::class)->name('admin.audit');
    Route::get('/admin/social', \App\Livewire\Admin\SocialModeration::class)->name('admin.social.moderation');
    Route::get('/admin/reembolsos', \App\Livewire\Admin\RefundsAdminPanel::class)->name('admin.refunds');

    // Admin — Financial management
    Route::get('/admin/financeiro', \App\Livewire\Admin\Financial::class)->name('admin.financial')->middleware('admin.module:financeiro');
    Route::get('/admin/comissoes', \App\Livewire\Admin\Commissions::class)->name('admin.commissions')->middleware('admin.module:financeiro');
    Route::get('/admin/saques', \App\Livewire\Admin\Payouts::class)->name('admin.payouts')->middleware('admin.module:financeiro');
    Route::get('/admin/categorias', \App\Livewire\Admin\Categories::class)->name('admin.categories')->middleware('admin.module:financeiro');
    Route::get('/admin/taxas', \App\Livewire\Admin\Fees::class)->name('admin.fees')->middleware('admin.module:financeiro');

    // Admin — Support
    Route::get('/admin/notificacoes-massa', \App\Livewire\Admin\MassNotifications::class)->name('admin.notifications.mass')->middleware('admin.module:suporte');

    // Admin — Settings (master only)
    Route::get('/admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings')->middleware('admin.module:settings');
});
