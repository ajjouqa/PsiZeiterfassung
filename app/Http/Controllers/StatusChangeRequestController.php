<?php

namespace App\Http\Controllers;

use App\Models\StatusChangeRequest;
use App\Models\DailyStatus;
use Auth;
use Illuminate\Http\Request;
use Notification;
use App\Notifications\NewStatusChangeRequestNotification;

class StatusChangeRequestController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'date' => 'required|date',
            'requested_status' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        StatusChangeRequest::create([
            'requested_id' => Auth::user()->id,
            'date' => $validated['date'],
            'requested_status' => $validated['requested_status'],
            'reason' => $validated['reason'],
        ]);


        return back()->with('success', 'Request submitted!');
    }

    public function index()
    {
        // for admin
        $requests = StatusChangeRequest::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.requests.index', compact('requests'));
    }

    public function update(Request $request, StatusChangeRequest $req)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $req->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        if ($request->status === 'approved') {
            
            DailyStatus::updateOrCreate(
                ['user_id' => $req->user_id, 'date' => $req->date],
                ['status' => $req->requested_status]
            );
        }

        return back()->with('success', 'Request processed.');
    }
}
