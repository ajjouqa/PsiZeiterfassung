<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\XmppPresenceLog;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function AdminDashboard()
    {
        $onlineUsers = DB::table('xmpp_user_mappings')
            ->where('current_presence', 'available')
            ->count();
        
        $mitarbeiters = User::all()->count();
        $admins = DB::table('admins')->count();
        $azubis = DB::table('azubis')->count();
        $totalUsers = $mitarbeiters + $admins + $azubis;

        $logs = XmppPresenceLog::query()
        ->with('xmppMapping')
        ->orderBy('timestamp', 'desc')
        ->take(10)->get();

        
        return view('admin.dashboard.index', compact('onlineUsers', 'mitarbeiters', 'admins', 'azubis', 'totalUsers', 'logs'));
    }

    public function AzubiDashboard()
    {
        return view('azubi.dashboard.index');
    }

    public function MitarbeiterDashboard()
    {
        return view('mitarbeiter.dashboard.index');
    }
}
