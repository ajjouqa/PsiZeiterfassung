<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ProcessXmppHangingSessions;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        ProcessXmppHangingSessions::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('xmpp:process-hanging-sessions')->everyMinute();
        $schedule->command('xmpp:cleanup-sessions')->everyFiveMinutes();
        
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
