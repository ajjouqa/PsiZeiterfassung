<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Models\Logs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogLoginListener
{
    public function handle(LoginEvent $event)
    {
        Log::info("Login Event Triggered for Employee ID: " . $event->user_id);

        $todayLog = Logs::where('user_id', $event->user_id)
        ->whereDate('login_time', now()->toDateString())
        ->whereNull('logout_time')
        ->latest()
        ->first();

        if(!$todayLog || $todayLog->logout_time !== null) {
            Logs::create([
                'user_id' => $event->user_id,
                'login_time' => $event->loginTime,
                'role' => $event->role,
            ]);
        }
    }
}
