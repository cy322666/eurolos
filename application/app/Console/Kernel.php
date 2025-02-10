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
         $schedule->command('app:get-lead-create')->dailyAt('01:00');
         $schedule->command('app:get-lead-statuses')->dailyAt('02:00');
         $schedule->command('app:get-updated-leads')->dailyAt('03:00');
         $schedule->command('app:get-leads')->dailyAt('04:00');
         $schedule->command('app:get-calls')->dailyAt('05:00');

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
