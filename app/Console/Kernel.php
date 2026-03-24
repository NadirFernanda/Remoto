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
        \App\Console\Commands\ExpireCreatorSubscriptions::class,
        \App\Console\Commands\SendReviewReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Atualizar taxa AOA de hora em hora
        $schedule->command('refresh:aoa-rate')->hourly();

        // Expirar subscrições de criadores vencidas + lembretes de renovação
        // Executar às 02:00 (fora do horário de pico — África/Luanda)
        $schedule->command('subscriptions:expire')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Lembretes de avaliação para projectos concluídos sem review
        // Executar às 10:00 para maximizar taxa de abertura
        $schedule->command('reviews:remind')
            ->dailyAt('10:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
