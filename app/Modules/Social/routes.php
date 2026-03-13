<?php

use Illuminate\Support\Facades\Route;

// ─── Social Module Routes ─────────────────────────────────────────────────────

// Public: anyone can browse feed and creator profiles
Route::get('/social', \App\Livewire\Social\Feed::class)->name('social.feed');
Route::get('/social/criador/{user}', \App\Livewire\Social\CreatorProfile::class)->name('social.creator');

// Authenticated: create post, bookmarks, stories
Route::middleware('auth')->group(function () {
    Route::get('/social/publicar', \App\Livewire\Social\CreatePost::class)->name('social.create');
    Route::get('/social/guardados', function () {
        return redirect('/social?bookmarkedOnly=1');
    })->name('social.bookmarks');
    Route::get('/social/minhas-publicacoes', function () {
        return redirect('/social?myPostsOnly=1');
    })->name('social.myposts');

    // Creator profile management
    Route::prefix('creator')->name('creator.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Creator\Dashboard::class)->name('dashboard');
        Route::get('/activar/{profile?}', \App\Livewire\Creator\ActivateProfile::class)->name('activate');
    });
});
