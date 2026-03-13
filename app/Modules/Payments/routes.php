<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payments\Controllers\ServiceEscrowController;
use App\Modules\Payments\Controllers\ServiceRefundController;
use App\Modules\Payments\Controllers\TransactionHistoryController;
use App\Modules\Payments\Controllers\FinanceHistoryExportController;
use App\Modules\Payments\Controllers\ReceiptController;

// ─── Payments Module Routes ───────────────────────────────────────────────────

Route::middleware(['web', 'auth'])->group(function () {
    // Escrow & refunds
    Route::post('/servico/{service}/liberar-pagamento', [ServiceEscrowController::class, 'releasePayment'])->name('service.payment.release');
    Route::post('/servico/{service}/solicitar-reembolso', [ServiceRefundController::class, 'requestRefund'])->name('service.refund.request');

    // Client payment flows
    Route::get('/cliente/servico/{service}/valor', \App\Livewire\Client\ServiceValue::class)->name('client.value');
    Route::get('/cliente/servico/{service}/pagamento', \App\Livewire\Client\PaymentEscrow::class)->name('client.payment');
    Route::get('/cliente/pagamentos', \App\Livewire\Client\FinanceHistory::class)->name('client.payments');
    Route::get('/cliente/financeiro/export', [FinanceHistoryExportController::class, 'exportCsv'])->name('client.finance.exportCsv');
    Route::get('/cliente/recibo/{service}', [ReceiptController::class, 'download'])->name('client.receipt.download');

    // Freelancer financial
    Route::get('/freelancer/financeiro', \App\Livewire\Freelancer\FinancialPanel::class)->name('freelancer.financial');
    Route::get('/freelancer/carteira', \App\Livewire\Freelancer\Wallet::class)->name('freelancer.wallet');
    Route::get('/freelancer/carteira/historico', \App\Livewire\Freelancer\WalletHistory::class)->name('freelancer.wallet.history');
    Route::get('/freelancer/afiliados', \App\Livewire\Freelancer\AffiliatePanel::class)->name('freelancer.affiliate');
    Route::get('/freelancer/patrocinio', \App\Livewire\Freelancer\SponsorshipPanel::class)->name('freelancer.sponsorship');

    // Transaction history
    Route::get('/transacoes', [TransactionHistoryController::class, 'index'])->name('transactions.history');

    // Affiliate link generation
    Route::post('/affiliate/generate', function () {
        $user = auth()->user();
        if (!$user->affiliate) {
            \App\Models\Affiliate::create([
                'user_id' => $user->id,
                'code' => strtoupper(\Illuminate\Support\Str::random(8)),
            ]);
        }
        return back()->with('success', 'Link de afiliado gerado!');
    })->name('affiliate.generate');

    // Client finance reports
    Route::get('/cliente/relatorios', \App\Livewire\Client\OrderHistory::class)->name('client.reports');
    Route::get('/cliente/financeiro', \App\Livewire\Client\FinancePanel::class)->name('client.finance');
    Route::get('/cliente/reembolsos', \App\Livewire\Client\RefundsPanel::class)->name('client.refunds');
    Route::get('/cliente/solicitar-reembolso', \App\Livewire\Client\RefundRequest::class)->name('client.refund.request');
    Route::get('/cliente/publicar-projeto', \App\Livewire\Client\PublishRequest::class)->name('client.publish.request');
});
