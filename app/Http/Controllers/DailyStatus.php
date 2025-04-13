<?php

namespace App\Http\Controllers;

use App\Models\XmppDailyPresenceSummary;
use DB;
use Illuminate\Http\Request;


class DailyStatus extends Controller
{
    //


    public function updateOvertime(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'overtime' => 'required|numeric',
                'summarie_id' => 'required|exists:daily_statuses,id',
            ]);

            $XmppDailyPresenceSummary = XmppDailyPresenceSummary::findOrFail($validated['summarie_id']);
            $XmppDailyPresenceSummary->over_time = $validated['overtime'];
            $XmppDailyPresenceSummary->save();

            DB::commit();
            return back()->with('success', 'Overtime updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error while executing opiration');
        }
    }

    public function updateStatus(Request $request)
    {
        
        DB::beginTransaction();
        try {

            $validated = $request->validate([
                'status' => 'required|in:working,sick,off,school',
                'notes' => 'nullable|string|max:255',
            ]);

            $DailyStatus = \App\Models\DailyStatus::findOrFail($request->summary_id);
            $DailyStatus->status = $validated['status'];
            $DailyStatus->notes = $validated['notes'];
            $DailyStatus->save();

            DB::commit();
            return back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error while executing opiration');
        }
    }
}
