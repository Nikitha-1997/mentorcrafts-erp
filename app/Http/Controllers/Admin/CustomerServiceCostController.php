<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerServiceCost;

class CustomerServiceCostController extends Controller
{
    public function index()
    {
        $costs = CustomerServiceCost::with(['customer', 'service'])
            ->whereNull('approved_amount')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.customer_service_costs.index', compact('costs'));
    }
    // Approve a service cost
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:0',
        ]);

        $cost = CustomerServiceCost::findOrFail($id);
        $cost->update([
            'approved_amount' => $request->approved_amount,
        ]);

        return redirect()->back()->with('success', 'Service cost approved successfully.');
    }
}
