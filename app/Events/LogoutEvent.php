<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogoutEvent
{
    use Dispatchable, SerializesModels;

    public $user_id;
    public $logout_time;
    public $role;

    public function __construct($user_id, $role)
    {
        $this->user_id = $user_id;
        $this->logout_time = now(); 
        $this->role = $role;
    }
}
