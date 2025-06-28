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
        $schedule->command('app:get-updated-leads')->dailyAt('06:10');
        $schedule->command('app:get-leads')->dailyAt('06:20');
        $schedule->command('app:get-updated-leads')->dailyAt('06:30');
        $schedule->command('app:get-leads')->dailyAt('06:40');
        $schedule->command('app:get-updated-leads')->dailyAt('06:50');
        $schedule->command('app:get-leads')->dailyAt('07:00');
        $schedule->command('app:get-updated-leads')->dailyAt('07:10');
        $schedule->command('app:get-leads')->dailyAt('07:20');
        $schedule->command('app:get-updated-leads')->dailyAt('07:30');
        $schedule->command('app:get-leads')->dailyAt('07:40');
        $schedule->command('app:get-updated-leads')->dailyAt('07:50');
        $schedule->command('app:get-leads')->dailyAt('08:00');
        $schedule->command('app:get-updated-leads')->dailyAt('08:10');
        $schedule->command('app:get-calls')->dailyAt('08:20');
        $schedule->command('app:get-updated-leads')->dailyAt('08:30');
        $schedule->command('app:get-calls')->dailyAt('10:10');

        $schedule->command('app:get-updated-leads')->dailyAt('09:00');
        $schedule->command('app:get-lead-create')->dailyAt('09:05');
        $schedule->command('app:get-leads')->dailyAt('09:10');

        $schedule->command('app:get-updated-leads')->dailyAt('10:00');
        $schedule->command('app:get-lead-create')->dailyAt('10:05');
        $schedule->command('app:get-leads')->dailyAt('10:10');

        $schedule->command('app:get-updated-leads')->dailyAt('11:00');
        $schedule->command('app:get-lead-create')->dailyAt('11:05');
        $schedule->command('app:get-leads')->dailyAt('11:10');

        $schedule->command('app:get-updated-leads')->dailyAt('12:00');
        $schedule->command('app:get-lead-create')->dailyAt('12:05');
        $schedule->command('app:get-leads')->dailyAt('12:10');

        $schedule->command('app:get-calls')->dailyAt('12:10');
        $schedule->command('app:get-calls')->dailyAt('14:10');
        $schedule->command('app:get-calls')->dailyAt('16:15');

        $schedule->command('app:get-entities')->dailyAt('17:00');
        $schedule->command('app:get-updated-leads')->dailyAt('17:01');
        $schedule->command('app:get-lead-statuses')->dailyAt('17:03');
        $schedule->command('app:get-calls')->dailyAt('17:05');

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
