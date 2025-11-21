<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Followup;
use Carbon\Carbon;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FollowupController extends Controller
{
  /*public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'notes' => 'required|string',
            'next_followup_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();

        Followup::create($validated);

        // OPTIONAL: schedule a notification or reminder for the admin
        // if($validated['next_followup_date']) {
        //     Notification logic can go here (Mail, DB, etc.)
        // }

        return redirect()->back()->with('success', 'Follow-up added successfully!');
    }*/
       public function store(Request $request)
{
    $validated = $request->validate([
        'lead_id' => 'required|exists:leads,id',
        'notes' => 'required|string',
        'next_followup_date' => 'nullable|date',
    ]);

    $validated['user_id'] = Auth::id();

    // 1️⃣ Create the follow-up record
    $followup = Followup::create($validated);

    // 2️⃣ If a next follow-up date is provided, create a reminder entry
    if (!empty($validated['next_followup_date'])) {

        $lead = $followup->lead; // Retrieve the related lead

        \App\Models\Reminder::create([
            'title'        => "Follow-up with {$lead->company_name}", // ✅ Same format as LeadController
            'description'  => $validated['notes'],
            'remind_at'    => $validated['next_followup_date'],
            'type'         => 'lead_followup',
            'related_id'   => $validated['lead_id'], // link reminder to the lead
        ]);

        Log::info('Reminder created for new follow-up', [
            'lead_id'   => $validated['lead_id'],
            'lead_name' => $lead->company_name,
            'remind_at' => $validated['next_followup_date'],
            'created_by'=> Auth::id(),
        ]);
    }

    return redirect()->back()->with('success', 'Follow-up and reminder added successfully!');
}


    public function destroy($id)
    {
        $followup = Followup::findOrFail($id);
        $followup->delete();

        return redirect()->back()->with('success', 'Follow-up deleted successfully.');
    }
}
