<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Azubi;
use App\Models\User;
use App\Models\XmppUserMapping;
use Illuminate\Http\Request;
use App\Services\XmppAuthService;
use Carbon\Carbon;

class XmppPresenceController extends Controller
{
    protected $xmppAuthService;
    
    public function __construct(XmppAuthService $xmppAuthService)
    {
        $this->xmppAuthService = $xmppAuthService;
    }
    
    /**
     * Show user's presence logs
     */
    public function showPresenceLogs(Request $request, $userType, $userId)
    {

        $userId = decrypt($userId);
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
        
        $logs = $this->xmppAuthService->getUserPresenceLogs($userType, $userId, $startDate, $endDate);
        
        $onlineTime = $this->xmppAuthService->calculateOnlineTime($userType, $userId, $startDate, $endDate);
        
        if($userType == 'azubi') {
            $username = Azubi::findOrFail($userId)->name;
        } elseif($userType == 'admin') {
            $username = Admin::findOrFail($userId)->name;
        } else {
            $username = User::findOrFail($userId)->name;
        }

        $status = XmppUserMapping::where('user_id', $userId)->first()?->current_presence;

        return view('xmpp.presence_logs', compact('logs', 'onlineTime', 'userType', 'username', 'status'));
    }
    
    /**
     * Show user's daily presence summaries
     */

    public function showDailySummaries(Request $request, $userType, $userId)
    {
        $userId = decrypt($userId);
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        $summaries = $this->xmppAuthService->getDailyPresenceSummaries($userType, $userId, $startDate, $endDate);
        
        // Calculate totals
        $totalSeconds = $summaries->sum('total_seconds');
        $totalSessions = $summaries->sum('session_count');
        $formattedTotal = $this->formatTimeInterval($totalSeconds);
        
        if($userType == 'azubi') {
            $username = Azubi::findOrFail($userId)->name;
        } elseif($userType == 'admin') {
            $username = Admin::findOrFail($userId)->name;
        } else {
            $username = User::findOrFail($userId)->name;
        }
        $status = XmppUserMapping::where('user_id', $userId)->first()?->current_presence;
        return view('xmpp.daily_summaries', compact(
            'summaries', 'userType', 'userId', 'startDate', 'endDate', 
            'totalSeconds', 'totalSessions', 'formattedTotal', 'username', 'status'
        ));
    }
    
    /**
     * Format time interval for view
     */
    private function formatTimeInterval($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}