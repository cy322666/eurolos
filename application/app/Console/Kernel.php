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
        //9 - 9.30
        //
        $schedule->command('app:get-entities')->dailyAt('08:00');
        $schedule->command('app:get-lead-create')->dailyAt('08:00');
        $schedule->command('app:get-lead-statuses')->dailyAt('08:05');
        $schedule->command('app:get-updated-leads')->dailyAt('08:10');
        $schedule->command('app:get-calls')->dailyAt('08:10');
        $schedule->command('app:get-leads')->dailyAt('08:15');

        $schedule->command('app:get-entities')->dailyAt('20:00');
        $schedule->command('app:get-lead-create')->dailyAt('20:00');
        $schedule->command('app:get-lead-statuses')->dailyAt('20:05');
        $schedule->command('app:get-updated-leads')->dailyAt('20:10');
        $schedule->command('app:get-calls')->dailyAt('20:10');
        $schedule->command('app:get-leads')->dailyAt('20:15');

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
