<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Messaging\Controllers\ChatFileUploadController;
use App\Modules\Messaging\Controllers\ChatSendController;
use App\Modules\Messaging\Controllers\ChatMessageController;

// ─── Messaging Module Routes ──────────────────────────────────────────────────

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/mensagens', \App\Livewire\Chat\ConversationInbox::class)->name('chat.inbox');
    Route::get('/chat/servico/{service}', \App\Livewire\Chat\ServiceChat::class)->name('service.chat');
    Route::post('/chat/upload-file', [ChatFileUploadController::class, 'upload'])->name('chat.upload.file');
    Route::post('/chat/servico/{service}/enviar', [ChatSendController::class, 'send'])->name('chat.send');
    Route::patch('/chat/mensagem/{message}', [ChatMessageController::class, 'update'])->name('chat.message.update');
    Route::delete('/chat/mensagem/{message}', [ChatMessageController::class, 'destroy'])->name('chat.message.destroy');
});
