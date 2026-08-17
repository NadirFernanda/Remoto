<?php

use Illuminate\Support\Facades\Route;

// ─── Wallet Module Routes ─────────────────────────────────────────────────────
// Rotas de carteira e financeiro do freelancer, separadas do módulo Payments.
// Todas as rotas financeiras do freelancer exigem autenticação + KYC aprovado.

Route::middleware(['web', 'auth', 'role:freelancer', 'kyc.verified'])->group(function () {
    Route::get('/freelancer/financeiro', \App\Livewire\Freelancer\FinancialPanel::class)
        ->name('freelancer.financial');

    Route::get('/freelancer/carteira', \App\Livewire\Freelancer\Wallet::class)
        ->name('freelancer.wallet');

    Route::get('/freelancer/carteira/historico', \App\Livewire\Freelancer\WalletHistory::class)
        ->name('freelancer.wallet.history');

    Route::get('/freelancer/patrocinio', \App\Livewire\Freelancer\SponsorshipPanel::class)
        ->name('freelancer.sponsorship');
});
