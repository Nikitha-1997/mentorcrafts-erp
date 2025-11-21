<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function admin()
    {
        // Admin sees all upcoming reminders
        $upcomingReminders = Reminder::where('remind_at', '>=', now())
                                     ->orderBy('remind_at')
                                     ->get();

        return view('admin.dashboard', compact('upcomingReminders'));
    }
    public function employee()
    {
        // Employee sees only their own reminders (if assigned)
        $upcomingReminders = Reminder::where('remind_at', '>=', now())
                                     ->where('related_id', Auth::id()) // or a proper assigned_to field
                                     ->orderBy('remind_at')
                                     ->get();

        return view('employee.dashboard', compact('upcomingReminders'));
    }

}
