<?php

use Illuminate\Support\Facades\Route;

// ─── Wallet Module Routes ─────────────────────────────────────────────────────
// Rotas de carteira e financeiro do freelancer, separadas do módulo Payments.
// Todas as rotas financeiras do freelancer exigem autenticação + KYC aprovado.

Route::middleware(['web', 'auth', 'kyc.verified'])->group(function () {
    Route::get('/freelancer/financeiro', \App\Livewire\Freelancer\FinancialPanel::class)
        ->name('freelancer.financial');

    Route::get('/freelancer/carteira', \App\Livewire\Freelancer\Wallet::class)
        ->name('freelancer.wallet');

    Route::get('/freelancer/carteira/historico', \App\Livewire\Freelancer\WalletHistory::class)
        ->name('freelancer.wallet.history');

    Route::get('/freelancer/afiliados', \App\Livewire\Freelancer\AffiliatePanel::class)
        ->name('freelancer.affiliate');

    Route::get('/freelancer/patrocinio', \App\Livewire\Freelancer\SponsorshipPanel::class)
        ->name('freelancer.sponsorship');
});

Route::middleware(['web', 'auth'])->group(function () {
    // Geração do link de afiliado (não requer KYC — apenas autenticação)
    Route::post('/affiliate/generate', function () {
        (new \App\Services\AffiliateService())->generateCode(auth()->user());
        return back()->with('success', 'Link de afiliado gerado!');
    })->name('affiliate.generate');
});
