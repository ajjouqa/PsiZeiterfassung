<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XmppAuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class XmppHeartbeatController extends Controller
{
    protected $xmppAuthService;

    public function __construct(XmppAuthService $xmppAuthService)
    {
        $this->xmppAuthService = $xmppAuthService;
    }

    public function update(Request $request)
    {
        $user = $request->user('azubi');
        if (!$user) {
            Log::error('XMPP heartbeat failed: no authenticated azubi user');
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Update last activity timestamp
        Session::put('xmpp_last_activity', now()->timestamp);
        
        $mapping = $this->xmppAuthService->getUserMapping('azubi', $user->id);
        if ($mapping) {
            // If user is marked as unavailable but is active, update to available
            if ($mapping->current_presence === 'unavailable') {
                $presenceLog = $this->xmppAuthService->recordPresenceEvent($mapping, 'auto_login', 'available');
                $mapping->current_presence = 'available';
                $mapping->last_login = now();
                $mapping->save();
                
                // Update daily presence summary
                if ($presenceLog) {
                    $this->xmppAuthService->updateDailyPresenceSummaryLogin($mapping, $presenceLog->timestamp);
                }
            } else {
                // Just update the timestamp
                $mapping->presence_updated_at = now();
                $mapping->save();
            }
        }

        Log::debug("XMPP heartbeat received for user ID: {$user->id}");
        return response()->json(['status' => 'ok']);
    }

    public function disconnect(Request $request)
    {
        $user = $request->user('azubi');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        Log::info("Browser disconnect notification received for user ID: {$user->id}");
        
        $mapping = $this->xmppAuthService->getUserMapping('azubi', $user->id);
        if (!$mapping) {
            return response()->json(['error' => 'No XMPP mapping found'], 404);
        }
        
        // Record logout event
        $presenceLog = $this->xmppAuthService->recordPresenceEvent($mapping, 'browser_disconnect', 'unavailable');
        
        // Update daily presence summary
        if ($presenceLog) {
            $this->xmppAuthService->updateDailyPresenceSummaryLogout($mapping, $presenceLog->timestamp);
        }
        
        // Update mapping
        $mapping->current_presence = 'unavailable';
        $mapping->last_logout = now();
        $mapping->save();

        return response()->json(['status' => 'disconnect_recorded']);
    }
}   