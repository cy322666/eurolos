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
        $schedule->command('app:get-calls')->dailyAt('08:10');
        $schedule->command('app:get-calls')->dailyAt('10:10');

        $schedule->command('app:get-lead-create')->dailyAt('11:00');

        $schedule->command('app:get-calls')->dailyAt('12:10');
        $schedule->command('app:get-calls')->dailyAt('14:10');
        $schedule->command('app:get-calls')->dailyAt('16:15');

        $schedule->command('app:get-entities')->dailyAt('17:00');
        $schedule->command('app:get-lead-statuses')->dailyAt('17:01');
        $schedule->command('app:get-updated-leads')->dailyAt('17:02');
        $schedule->command('app:get-calls')->dailyAt('17:03');

        $schedule->command('app:get-lead-create')->dailyAt('16:00');

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
