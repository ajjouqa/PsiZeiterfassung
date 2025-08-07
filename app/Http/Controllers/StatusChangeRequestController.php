<?php

namespace App\Http\Controllers;

use App\Models\StatusChangeRequest;
use App\Models\DailyStatus;
use App\Models\XmppDailyPresenceSummary;
use App\Traits\DetectsUserRole;
use Auth;
use Illuminate\Http\Request;
use Notification;
use App\Notifications\NewStatusChangeRequestNotification;

class StatusChangeRequestController extends Controller
{
    use DetectsUserRole;
    public function store(Request $request)
    {

        $validated = $request->validate([
            'date' => 'required|date',
            'requested_status' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        StatusChangeRequest::create([
            'date' => $validated['date'],
            'requested_status' => $validated['requested_status'],
            'requester_type' => $this->detectUserRole(),
            'requester_id' => Auth::user()->id,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Request submitted!');
    }

    public function index()
    {
        // for admin
        $requests = StatusChangeRequest::with('azubi', 'mitarbeiter')->orderBy('created_at', 'desc')->get();
        return view('notifications.index', compact('requests'));
    }

    public function show($id)
    {
        $id = decrypt($id);
        $request = StatusChangeRequest::findOrFail($id);
        return view('xmpp.modifyDailyStatus', compact('request'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
            'requester_type' => 'required|in:azubi,mitarbeiter,admin',
            'requested_status' => 'required|string',
            'requester_id' => 'required',
            'xmpp_username' => 'required|string', 
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'id' => 'required|exists:status_change_requests,id',
        ]);
        


        if ($request->status === 'approved') {
            $status = XmppDailyPresenceSummary::where('user_type', $request->requester_type)
                ->where('user_id', $request->requester_id)
                ->where('date', $request->date)
                ->first();
            if ($status) {
                DailyStatus::updateOrCreate(
                    ['daily_summary_id' => $status->id],
                    [
                        'status' => $request->requested_status,
                        'notes' => $request->reason,
                    ]
                );
                StatusChangeRequest::where('id', $request->id)->update([
                    'status' => $request->status,
                    'admin_note' => $request->admin_note,
                ]);
            } else {
                XmppDailyPresenceSummary::create([
                    'xmpp_username' => $request->xmpp_username, 
                    'user_type' => $request->requester_type,
                    'user_id' => $request->requester_id,
                    'date' => $request->date,
                    'total_seconds' => 0,
                    'formatted_time' => '00:00:00',
                    'session_count' => 0,
                    'first_login' => null,
                    'last_logout' => null,
                ]);
                $status = XmppDailyPresenceSummary::where('user_type', $request->requester_type)
                    ->where('user_id', $request->requester_id)
                    ->where('date', $request->date)
                    ->first();
                DailyStatus::create([
                    'daily_summary_id' => $status->id,
                    'status' => $request->requested_status,
                    'notes' => $request->reason,
                ]);
            }

            return back()->with('success', 'Approuved');
        }elseif ($request->status === 'rejected') {
            StatusChangeRequest::where('id', $request->id)->update([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
            ]);
            return back()->with('success', 'Rejected');
        }

    }
    public function destroy($id)
    {
        $id = decrypt($id);
        $request = StatusChangeRequest::findOrFail($id);
        $request->delete();
        return back()->with('success', 'Request deleted successfully');
    }
    
}