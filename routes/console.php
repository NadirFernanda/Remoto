<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\InfoprodutoPatrocinio;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire infoproduto sponsorships daily
Schedule::call(function () {
    InfoprodutoPatrocinio::where('status', 'ativo')
        ->where('data_fim', '<', today())
        ->update(['status' => 'expirado']);
})->daily()->name('expire-infoproduto-patrocinios');
