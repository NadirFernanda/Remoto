<?php

use Illuminate\Support\Facades\Route;

// ─── Social Module Routes ─────────────────────────────────────────────────────

// Feed and creator profiles require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/social', \App\Livewire\Social\Feed::class)->name('social.feed');
    Route::get('/social/criadores', \App\Livewire\Social\CreatorSearch::class)->name('social.creators');
    Route::get('/social/criador/{user}', \App\Livewire\Social\CreatorProfile::class)->name('social.creator');
});

// Authenticated: create post, bookmarks, stories (todos os roles)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/social/publicar', \App\Livewire\Social\CreatePost::class)->name('social.create');
    Route::get('/social/guardados', function () {
        return redirect('/social?bookmarkedOnly=1');
    })->name('social.bookmarks');
    Route::get('/social/minhas-publicacoes', \App\Livewire\Social\MyPosts::class)->name('social.myposts');
});

// Creator dashboard (role:creator obrigatório)
Route::middleware(['web', 'auth', 'role:creator'])->prefix('creator')->name('creator.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Creator\Dashboard::class)->name('dashboard');
    Route::get('/activar/{profile?}', \App\Livewire\Creator\ActivateProfile::class)->name('activate');
});

// Assinaturas acessível a qualquer utilizador autenticado com perfil de criador
// (a verificação de has_creator_profile é feita dentro do componente)
Route::middleware(['web', 'auth'])->prefix('creator')->name('creator.')->group(function () {
    Route::get('/assinaturas', \App\Livewire\Creator\SubscriptionManager::class)->name('subscriptions');
});
