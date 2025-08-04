<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\XmppAuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class XmppSessionMonitor
{
    protected $xmppAuthService;

    public function __construct(XmppAuthService $xmppAuthService)
    {
        $this->xmppAuthService = $xmppAuthService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Set the last activity timestamp when a user makes a request
        if ($request->user('azubi')) {
            Session::put('xmpp_last_activity', now()->timestamp);
        }
        
        return $next($request);
    }

    public function terminate($request, $response)
    {
        
        $user = $request->user('azubi');
        
        // Check for session expiration or logout
        if (!$user && Session::has('xmpp_was_logged_in')) {
            // User was logged in before but not now
            $userId = Session::get('xmpp_was_logged_in');
            Log::info("Session ended for user ID: {$userId}");
            
            // Get mapping and update status
            $mapping = $this->xmppAuthService->getUserMapping('azubi', $userId);
            if ($mapping && $mapping->current_presence !== 'unavailable') {
                $this->xmppAuthService->recordPresenceEvent($mapping, 'session_ended', 'unavailable');
                
                // Update daily presence summary
                $presenceLog = $this->xmppAuthService->recordPresenceEvent($mapping, 'logout', 'unavailable');
                if ($presenceLog) {
                    $this->xmppAuthService->updateDailyPresenceSummaryLogout($mapping, $presenceLog->timestamp);
                }
                
                // Update mapping
                $mapping->current_presence = 'unavailable';
                $mapping->last_logout = now();
                $mapping->save();
            }
            
            // Remove the session variable
            Session::forget('xmpp_was_logged_in');
        } 
        // If user is logged in, keep track of the user ID
        elseif ($user) {
            Session::put('xmpp_was_logged_in', $user->id);
        }
    }
}