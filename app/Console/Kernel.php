<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\DeleteAllServices::class,
        \App\Console\Commands\ConvertBriefingsToText::class,
        \App\Console\Commands\RefreshAoaRate::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Refresh AOA rate hourly
        $schedule->command('refresh:aoa-rate')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
