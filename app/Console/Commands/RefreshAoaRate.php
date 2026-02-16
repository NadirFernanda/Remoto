<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExchangeRateService;

class RefreshAoaRate extends Command
{
    protected $signature = 'refresh:aoa-rate {--force : Force refresh from remote}';
    protected $description = 'Refresh cached BRL->AOA exchange rate';

    public function handle(ExchangeRateService $service)
    {
        $this->info('Refreshing AOA rate...');
        $rate = $service->getRate($this->option('force'));
        $this->info('Current AOA rate: ' . $rate);
        return 0;
    }
}
