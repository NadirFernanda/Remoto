<?php

use Illuminate\Support\Facades\Route;

// ─── Loja Module Routes ───────────────────────────────────────────────────────

// Public store
Route::middleware('web')->group(function () {
    Route::get('/loja', \App\Livewire\Loja\Vitrine::class)->name('loja.index');
    Route::get('/loja/{produto:slug}', \App\Livewire\Loja\ProdutoDetalhe::class)->name('loja.show');
});

// Checkout — pagamento de produtos (saldo/Multicaixa Express/Referência)
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/loja/{produto:slug}/comprar', \App\Livewire\Loja\PurchaseCheckout::class)->name('loja.purchase');
});

// Authenticated seller routes
Route::middleware(['web', 'auth', 'role:freelancer'])->group(function () {
    Route::get('/freelancer/loja', \App\Livewire\Freelancer\Loja::class)->name('freelancer.loja');
});

// Admin loja — faltava role:admin, 2fa e admin.module aqui (encontrado em
// auditoria de segurança): dependia só do abort_if interno do componente,
// sem exigir 2FA nem restringir por admin_role como todas as outras
// páginas de admin.
Route::middleware(['web', 'auth', 'role:admin', '2fa', 'admin.module:gestor'])->group(function () {
    Route::get('/admin/loja', \App\Livewire\Admin\LojaAdmin::class)->name('admin.loja');
});
