<?php

namespace App\Http\View\Composers;

use App\Models\StatusChangeRequest;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    public function compose(View $view)
    {
        $notifications = collect();

        if (Auth::guard('admin')->check()) {
            $notifications = StatusChangeRequest::with('mitarbeiter', 'azubi')
            ->where('status', 'pending')
            ->take(5)
            ->get();

        } elseif (Auth::guard('web')->check()) {
            $notifications = StatusChangeRequest::with('mitarbeiter')
            ->where('status',['rejected', 'approved'])
            ->where('requester_type', 'mitarbeiter')
            ->where('requester_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        } elseif (Auth::guard('azubi')->check()) {
            $notifications = StatusChangeRequest::with('azubi')
            ->where('status', ['approved', 'rejected'])
            ->where('requester_type', 'azubi')
            ->where('requester_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        }

        $view->with('notifications', $notifications);
    }
}