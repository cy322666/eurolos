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
         $schedule->command('app:get-entities')->dailyAt('07:00');
         $schedule->command('app:get-lead-create')->hourly();
         $schedule->command('app:get-leads')->everyThirtyMinutes();
         $schedule->command('app:get-lead-statuses')->hourly();
         $schedule->command('app:get-calls')->hourly();

         $schedule->command('telescope:prune --hours=72')->daily();
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
