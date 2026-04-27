<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Controllers\ContractController;
use App\Modules\Admin\Controllers\UsersExportController;
use App\Modules\Admin\Controllers\FinancialExportController;
use App\Modules\Admin\Controllers\AuditExportController;
use App\Modules\Admin\Controllers\ReportsExportController;
use App\Modules\Admin\Services\ExchangeRateService;

// ─── Admin Module Routes ──────────────────────────────────────────────────────
// Base group: apenas verifica que o utilizador é admin (qualquer sub-role).
// Cada rota/grupo aplica depois o seu próprio admin.module:X específico.

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {

    // Dashboard — acessível a todos os admins
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');

    // Exchange rate refresh (gestor+)
    Route::post('/refresh-aoa-rate', function (ExchangeRateService $svc) {
        $rate = $svc->refresh();
        return response()->json(['status' => 'ok', 'rate' => $rate]);
    })->middleware('admin.module:gestor');

    // Commercial contracts management (gestor+)
    Route::resource('comercial', ContractController::class)->names('admin.comercial')->middleware('admin.module:gestor');

    // Gestão de utilizadores e serviços (gestor+)
    Route::get('/admin/users', \App\Livewire\Admin\Users::class)->name('admin.users')->middleware('admin.module:gestor');
    Route::get('/admin/services', \App\Livewire\Admin\Services::class)->name('admin.services')->middleware('admin.module:gestor');
    Route::get('/admin/disputas', \App\Livewire\Admin\DisputeAdmin::class)->name('admin.disputes')->middleware('admin.module:suporte');
    Route::get('/admin/suporte', \App\Livewire\Admin\AdminSupportTickets::class)->name('admin.support')->middleware('admin.module:suporte');
    Route::get('/admin/auditoria', \App\Livewire\Admin\AuditLogs::class)->name('admin.audit')->middleware('admin.module:audit');
    Route::get('/admin/social', \App\Livewire\Admin\SocialModeration::class)->name('admin.social.moderation')->middleware('admin.module:gestor');
    Route::get('/admin/reembolsos', \App\Livewire\Admin\RefundsAdminPanel::class)->name('admin.refunds')->middleware('admin.module:gestor');

    // Admin — Financial management (financeiro+)
    Route::get('/admin/financeiro', \App\Livewire\Admin\Financial::class)->name('admin.financial')->middleware('admin.module:financeiro');
    Route::get('/admin/comissoes', \App\Livewire\Admin\Commissions::class)->name('admin.commissions')->middleware('admin.module:financeiro');
    Route::get('/admin/saques', \App\Livewire\Admin\Payouts::class)->name('admin.payouts')->middleware('admin.module:financeiro');
    Route::get('/admin/categorias', \App\Livewire\Admin\Categories::class)->name('admin.categories')->middleware('admin.module:financeiro');
    Route::get('/admin/taxas', \App\Livewire\Admin\Fees::class)->name('admin.fees')->middleware('admin.module:financeiro');

    // Admin — Relatórios
    Route::get('/admin/relatorios/fluxo-caixa', \App\Livewire\Admin\CashFlow::class)->name('admin.reports.cashflow')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/extrato-contabilidade', \App\Livewire\Admin\AccountingStatement::class)->name('admin.reports.accounting')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/saques', \App\Livewire\Admin\WithdrawalReport::class)->name('admin.reports.withdrawals')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/servicos', \App\Livewire\Admin\ServicesReport::class)->name('admin.reports.services')->middleware('admin.module:financeiro');

    // Admin — Export reports (financeiro module)
    Route::get('/admin/relatorios/users/export', [UsersExportController::class, 'exportCsv'])->name('admin.reports.users.export')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/financeiro/export', [FinancialExportController::class, 'exportCsv'])->name('admin.reports.financial.export')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/auditoria/export', [AuditExportController::class, 'exportCsv'])->name('admin.reports.audit.export')->middleware('admin.module:audit');
    Route::get('/admin/relatorios/auditoria/export-excel', [AuditExportController::class, 'exportExcel'])->name('admin.reports.audit.export.excel')->middleware('admin.module:audit');
    Route::get('/admin/relatorios/auditoria/export-pdf', [AuditExportController::class, 'exportPdf'])->name('admin.reports.audit.export.pdf')->middleware('admin.module:audit');

    // Admin — Export: Fluxo de Caixa
    Route::get('/admin/relatorios/fluxo-caixa/csv',   [ReportsExportController::class, 'cashFlowCsv'])->name('admin.reports.cashflow.csv')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/fluxo-caixa/excel', [ReportsExportController::class, 'cashFlowExcel'])->name('admin.reports.cashflow.excel')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/fluxo-caixa/pdf',   [ReportsExportController::class, 'cashFlowPdf'])->name('admin.reports.cashflow.pdf')->middleware('admin.module:financeiro');

    // Admin — Export: Extrato Contabilidade
    Route::get('/admin/relatorios/extrato-contabilidade/csv',   [ReportsExportController::class, 'accountingCsv'])->name('admin.reports.accounting.csv')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/extrato-contabilidade/excel', [ReportsExportController::class, 'accountingExcel'])->name('admin.reports.accounting.excel')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/extrato-contabilidade/pdf',   [ReportsExportController::class, 'accountingPdf'])->name('admin.reports.accounting.pdf')->middleware('admin.module:financeiro');

    // Admin — Export: Saques
    Route::get('/admin/relatorios/saques/csv', [ReportsExportController::class, 'withdrawalsCsv'])->name('admin.reports.withdrawals.csv')->middleware('admin.module:financeiro');
    Route::get('/admin/relatorios/saques/pdf', [ReportsExportController::class, 'withdrawalsPdf'])->name('admin.reports.withdrawals.pdf')->middleware('admin.module:financeiro');

    // Admin — Support
    Route::get('/admin/notificacoes-massa', \App\Livewire\Admin\MassNotifications::class)->name('admin.notifications.mass')->middleware('admin.module:suporte');

    // Admin — Settings (master only)
    Route::get('/admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings')->middleware('admin.module:settings');

    // Admin — Manage Administrators (master only)
    Route::get('/admin/administradores', \App\Livewire\Admin\AdminManager::class)->name('admin.managers')->middleware('admin.module:admin-manager');

    // Admin — Download infoproduto file for moderation review
    Route::get('/admin/loja/download/{id}', function (int $id) {
        $produto = \App\Models\Infoproduto::findOrFail($id);

        if (!$produto->arquivo_path || !\Illuminate\Support\Facades\Storage::disk('private')->exists($produto->arquivo_path)) {
            abort(404, 'Ficheiro não encontrado.');
        }

        return \Illuminate\Support\Facades\Storage::disk('private')->download(
            $produto->arquivo_path,
            $produto->titulo . ' — ' . basename($produto->arquivo_path)
        );
    })->name('admin.loja.download');
});
