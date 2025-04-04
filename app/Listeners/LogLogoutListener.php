<?php

namespace App\Listeners;

use App\Events\LogoutEvent;
use App\Models\Logs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogLogoutListener
{
    /**
     * Create the event listener.
     */
    public function handle(LogoutEvent $event)
    {
        Log::info("Logout Event Triggered for Employee ID: " . $event->user_id);

        $todayLog = Logs::where('user_id', $event->user_id)
            ->whereDate('login_time', now()->toDateString())
            ->latest()
            ->first();

        if ($todayLog) {
            $todayLog->update([
                'logout_time' => now(),
            ]);
        }
    }
}
