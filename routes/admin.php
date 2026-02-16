<?php

use Illuminate\Support\Facades\Route;
use App\Services\ExchangeRateService;

Route::middleware(['web','auth'])->group(function () {
    Route::post('/refresh-aoa-rate', function (ExchangeRateService $svc) {
        $rate = $svc->refresh();
        return response()->json(['status' => 'ok', 'rate' => $rate]);
    });
});
