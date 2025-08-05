<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyStatusChangeRequested extends Notification
{
    use Queueable;

    protected $requester;
    protected $newStatus;
    protected $date;

    public function __construct($requester, $newStatus, $date)
    {
        $this->requester = $requester;
        $this->newStatus = $newStatus;
        $this->date = $date;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
            'new_status' => $this->newStatus,
            'date' => $this->date,
            'message' => "Status change requested by {$this->requester->name} to '{$this->newStatus}' for date {$this->date}.",
        ];
    }
    public static function notifyAllAdmins($requester, $newStatus, $date)
    {
        $admins = Admin::all();

        foreach ($admins as $admin) {
            $admin->notify(new self($requester, $newStatus, $date));
        }
        return back()->with('success', 'Request submitted successfully!');
    }

}
