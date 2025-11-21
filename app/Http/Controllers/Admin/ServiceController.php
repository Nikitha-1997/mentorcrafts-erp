<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCost;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

public function getData(Request $request)
{
    if ($request->ajax()) {
        $services = Service::with(['creator', 'costs'])->select('services.*');

        return DataTables::of($services)
            ->addIndexColumn()
            ->editColumn('status', function ($service) {
                return '<span class="badge '.($service->is_active ? 'bg-success' : 'bg-danger').'">'.
                       ($service->is_active ? 'Active' : 'Inactive').'</span>';
            })
            ->addColumn('created_by', function ($service) {
                return $service->creator?->name ?? 'System';
            })
            ->addColumn('total_cost', function ($service) {
                // Calculate total cost
                $total = $service->costs->sum('amount');
                return '₹' . number_format($total, 2);
            })
            ->addColumn('actions', function ($service) {
                $edit = '<a href="' . route('services.edit', $service->id) . '" class="btn btn-sm btn-warning me-1">
                            <i class="ri-edit-line"></i> Edit
                        </a>';
                $delete = '
                    <form action="' . route('services.destroy', $service->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this service?\')">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-sm btn-danger">
                            <i class="ri-delete-bin-line"></i> Delete
                        </button>
                    </form>';
                return $edit . $delete;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }
}
public function getServiceCosts($id)
{
    //$service = Service::with('costs')->find($id);
    $service = Service::with('costs')->findOrFail($id);
 // Format the data for JSON
    $costs = $service->costs->map(function ($cost) use ($service) {
        return [
            'service_id'   => $service->id,
            'service_name' => $service->name,
            'name'         => $cost->name,
            'amount'       => $cost->amount,
            'billing_type' => $cost->billing_type,
        ];
    });

    return response()->json($costs);
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:services,name|max:255',
            'description' => 'nullable|string',
            'cost_name.*' => 'required|string',
            'cost_amount.*' => 'required|numeric|min:0',
            'billing_type.*' => 'required|in:one_time,monthly,yearly,per_sqft',
        ]);

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        foreach ($request->cost_name as $index => $name) {
            ServiceCost::create([
                'service_id' => $service->id,
                'name' => $name,
                'amount' => $request->cost_amount[$index],
                'billing_type' => $request->billing_type[$index],
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }
    /**
     * Display the specified resource.
     */
public function show(Service $service)
{
    $service->load('customers'); // load related clients

    return view('admin.services.show', compact('service'));
    
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $service->load('costs');
        return view('admin.services.edit', compact('service'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'cost_name.*' => 'required|string',
            'cost_amount.*' => 'required|numeric|min:0',
            'billing_type.*' => 'required|in:one_time,monthly,yearly,per_sqft',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // 🔁 Remove old costs and re-add
        $service->costs()->delete();
        foreach ($request->cost_name as $index => $name) {
            ServiceCost::create([
                'service_id' => $service->id,
                'name' => $name,
                'amount' => $request->cost_amount[$index],
                'billing_type' => $request->billing_type[$index],
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }
    /**
     * 📌 New: Display all services with their clients (Teams-style layout)
     */
    public function teamView()
    {
        $services = Service::with('customers')->get();
        return view('admin.services.team_view', compact('services'));
    }
     /**
     * 📌 New: Display a single client's profile under a service
     */
    public function showCustomer(Service $service, Customer $customer)
    {
        // Ensure client belongs to this service
        if (!$service->customers->contains($customer->id)) {
            abort(403, 'Unauthorized access to this client profile.');
        }

        return view('admin.customers.profile', compact('service', 'customer'));
    }
    public function details(Service $service)
{
    $service->load(['customers', 'costs']); // eager load clients and costs

    return view('admin.services.details', compact('service'));
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
         $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
