<?php

namespace App\Modules\Loja;

use Illuminate\Support\ServiceProvider;

class LojaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
