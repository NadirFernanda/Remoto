<?php

use Illuminate\Support\Facades\Route;

// ─── Loja Module Routes ───────────────────────────────────────────────────────

// Public store
Route::get('/loja', \App\Livewire\Loja\Vitrine::class)->name('loja.index');
Route::get('/loja/{produto:slug}', \App\Livewire\Loja\ProdutoDetalhe::class)->name('loja.show');

// Authenticated seller routes
Route::middleware('auth')->group(function () {
    Route::get('/freelancer/loja', \App\Livewire\Freelancer\Loja::class)->name('freelancer.loja');
    Route::get('/admin/loja', \App\Livewire\Admin\LojaAdmin::class)->name('admin.loja');
});
