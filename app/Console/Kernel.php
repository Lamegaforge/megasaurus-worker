<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command('app:fetch-clips-command')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('app:update-clips-command')
            ->dailyAt('03:00')
            ->withoutOverlapping();

        $schedule
            ->command('app:update-clips-command --recent')
            ->everyTenMinutes()
            ->between('18:00', '01:00')
            ->withoutOverlapping();

        $schedule
            ->command('app:update-games-active-clip-count-command')
            ->dailyAt('05:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
