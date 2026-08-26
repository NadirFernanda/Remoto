<?php

use Illuminate\Support\Facades\Route;

// ─── Wallet Module Routes ─────────────────────────────────────────────────────
// Rotas de carteira e financeiro do freelancer, separadas do módulo Payments.
// Todas as rotas financeiras do freelancer exigem autenticação + KYC aprovado.

Route::middleware(['web', 'auth', 'role:freelancer', 'kyc.verified'])->group(function () {
    Route::get('/freelancer/financeiro', \App\Livewire\Freelancer\FinancialPanel::class)
        ->name('freelancer.financial');

    // Páginas antigas de saque/extrato — substituídas pelo Painel Financeiro
    // (único ponto de saque desde o fix do bug de saque duplicado). O
    // formulário de saque desta página tinha o seu próprio mínimo (Kz 1.000)
    // e nenhuma das regras vigentes no Painel Financeiro, o que a tornava um
    // desvio real à regra de saque mínimo — mantidas como redirect (nunca
    // 404) só para não partir marcadores/links antigos e notificações já
    // enviadas.
    Route::get('/freelancer/carteira', fn () => redirect()->route('freelancer.financial'))
        ->name('freelancer.wallet');

    Route::get('/freelancer/carteira/historico', fn () => redirect()->route('freelancer.financial'))
        ->name('freelancer.wallet.history');

    Route::get('/freelancer/patrocinio', \App\Livewire\Freelancer\SponsorshipPanel::class)
        ->name('freelancer.sponsorship');
});
