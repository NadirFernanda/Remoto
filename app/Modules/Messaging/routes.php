<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Messaging\Controllers\ChatFileUploadController;

// ─── Messaging Module Routes ──────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/chat/servico/{service}', \App\Livewire\Chat\ServiceChat::class)->name('service.chat');
    Route::post('/chat/upload-file', [ChatFileUploadController::class, 'upload'])->name('chat.upload.file');
});
