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
        $schedule->command('app:get-entities')->dailyAt('08:00');
        $schedule->command('app:get-lead-statuses')->dailyAt('08:01');
        $schedule->command('app:get-updated-leads')->dailyAt('08:02');
        $schedule->command('app:get-calls')->dailyAt('08:03');

        $schedule->command('app:get-lead-create')->dailyAt('05:00');
        $schedule->command('app:get-leads')->dailyAt('06:00');
        $schedule->command('app:get-leads')->dailyAt('06:20');
        $schedule->command('app:get-leads')->dailyAt('06:40');
        $schedule->command('app:get-leads')->dailyAt('07:00');
        $schedule->command('app:get-leads')->dailyAt('07:20');
        $schedule->command('app:get-leads')->dailyAt('07:40');
        $schedule->command('app:get-leads')->dailyAt('08:00');

        $schedule->command('app:get-entities')->dailyAt('17:00');
        $schedule->command('app:get-lead-statuses')->dailyAt('17:01');
        $schedule->command('app:get-updated-leads')->dailyAt('17:02');
        $schedule->command('app:get-calls')->dailyAt('17:03');

        $schedule->command('app:get-lead-create')->dailyAt('16:00');
//        $schedule->command('app:get-leads 0')->dailyAt('16:05');
//        $schedule->command('app:get-leads 2000')->dailyAt('16:15');
//        $schedule->command('app:get-leads 4000')->dailyAt('16:25');
//        $schedule->command('app:get-leads 6000')->dailyAt('16:35');
//        $schedule->command('app:get-leads 8000')->dailyAt('16:45');
//        $schedule->command('app:get-leads 10000')->dailyAt('16:45');

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
