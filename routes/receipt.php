<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ReceiptController;

Route::middleware(['auth'])->group(function () {
    Route::get('/cliente/recibo/{service}', [ReceiptController::class, 'download'])->name('client.receipt.download');
});
