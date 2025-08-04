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
        ->where('role', $event->role)
        ->first();
        
        if (!$todayLog){
            Logs::create([
                'user_id' => $event->user_id,
                'login_time' => null,
                'logout_time' => null,
                'role' => $event->role,
            ]);
        }
    }
}
