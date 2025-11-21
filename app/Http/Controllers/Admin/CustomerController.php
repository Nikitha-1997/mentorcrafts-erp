<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\CustomerServiceCost;
use App\Models\LeadService;
use App\Models\LeadServiceCost;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use \App\Models\ServiceCost;
use \App\Models\Project;


class CustomerController extends Controller
{
    // Display listing
    public function index()
    {
        $customers = Customer::with('services')->orderBy('id', 'asc')->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    // DataTables Ajax
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::with('services')->select('customers.*');

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('actions', function ($customer) {
                    return '
                        <a href="' . route('customers.show', $customer->id) . '" class="btn btn-sm btn-info">View</a>
                        <a href="' . route('customers.edit', $customer->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <form action="' . route('customers.destroy', $customer->id) . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Delete this customer?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                        </form>
                    ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    // Show edit form
    public function edit(Customer $customer)
{
    $services = Service::where('is_active', true)->get();
     $customer->load('serviceCosts.service'); // eager load cost details

    // Only pass services and existing customer data
    return view('admin.customers.edit', compact('customer', 'services'));
    
}


    // Show customer details
   /* public function show(Service $service, Customer $customer)
    {
        $customer->load('services', 'lead');
        return view('admin.customers.show', compact('service','customer'));
    }*/
public function show(Service $service, Customer $customer)
{
    $customer->load([
        'services',
        'lead',
        'projects.service' // load project with related service
    ]);

    return view('admin.customers.show', compact('service','customer'));
}


    // Update customer
public function update(Request $request, Customer $customer)
{
    $request->validate([
        'company_name'   => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'phone'          => 'nullable|string|max:20',
        'email'          => 'nullable|email',
        'address_line1'  => 'nullable|string|max:255',
        'address_line2'  => 'nullable|string|max:255',
        'country'        => 'nullable|string|max:255',
        'state'          => 'nullable|string|max:255',
        'district'       => 'nullable|string|max:255',
        'city'           => 'nullable|string|max:255',
        'pincode'        => 'nullable|string|max:10',
        'services'       => 'nullable|array',
    ]);

    // ✅ Update basic info
    $customer->update($request->only([
        'company_name', 'contact_person', 'phone', 'email',
        'address_line1', 'address_line2', 'country', 'state',
        'district', 'city', 'pincode'
    ]));

    // ✅ Sync services safely (without deleting existing)
    if ($request->filled('services')) {
        $existingServiceIds = $customer->services->pluck('id')->toArray();
        $newServiceIds = $request->services;

        // Preserve existing + add new
        $syncData = [];

        foreach ($newServiceIds as $serviceId) {
            $existing = $customer->services()->where('service_id', $serviceId)->first();
            if ($existing) {
                $syncData[$serviceId] = ['service_code' => $existing->pivot->service_code];
            } else {
                $service = Service::find($serviceId);
                $code = CustomerService::generateServiceCode($service->name);
                $syncData[$serviceId] = ['service_code' => $code];
            }
        }

        $customer->services()->syncWithoutDetaching($syncData);
    }

    // ✅ Update Customer Service Costs
    if ($request->has('costs')) {
        $submittedCostIds = [];

        foreach ($request->costs as $serviceId => $costData) {
            $ids = $costData['id'] ?? [];
            $names = $costData['name'] ?? [];
            $amounts = $costData['quoted_amount'] ?? [];
            $types = $costData['billing_type'] ?? [];

            foreach ($names as $i => $name) {
                $costId = $ids[$i] ?? null;

                if ($costId) {
                    // Update existing
                    $cost = $customer->serviceCosts()->where('id', $costId)->first();
                    if ($cost) {
                        $cost->update([
                            'name' => $name,
                            'quoted_amount' => $amounts[$i],
                            'billing_type' => $types[$i],
                        ]);
                        $submittedCostIds[] = $costId;
                    }
                } else {
                    // New cost entry
                    $newCost = $customer->serviceCosts()->create([
                        'service_id' => $serviceId,
                        'name' => $name,
                        'quoted_amount' => $amounts[$i],
                        'billing_type' => $types[$i],
                    ]);
                    $submittedCostIds[] = $newCost->id;
                }
            }
        }

        // ✅ Delete removed costs (not in form)
        $customer->serviceCosts()
            ->whereNotIn('id', $submittedCostIds)
            ->delete();
    }

    return redirect()
        ->route('customers.index')
        ->with('success', 'Customer updated successfully with service costs.');
}

public function requestNewService(Request $request, $customerId)
{
    $request->validate([
        'services' => 'required|array|min:1',
        'services.*' => 'exists:services,id',
    ]);

    $customer = Customer::findOrFail($customerId);

    // ✅ Create new lead
    $lead = Lead::create([
        'lead_code' => Lead::generateLeadCode(),
        'company_name' => $customer->company_name,
        'contact_person' => $customer->contact_person,
        'phone' => $customer->phone,
        'email' => $customer->email,
        'address_line1' => $customer->address_line1,
        'address_line2' => $customer->address_line2,
        'country' => $customer->country,
        'state' => $customer->state,
        'district' => $customer->district,
        'city' => $customer->city,
        'pincode' => $customer->pincode,
        'customer_id' => $customer->id,
        'status' => 'New',
        'requested_via_customer' => true,
        'created_by' => Auth::id(),
    ]);

    // ✅ Attach selected services to lead
    foreach ($request->services as $serviceId) {
        $lead->services()->attach($serviceId);

        // (Optional) Copy base cost templates from service_costs
        $baseCosts = ServiceCost::where('service_id', $serviceId)->get();
        foreach ($baseCosts as $cost) {
            LeadServiceCost::create([
                'lead_id' => $lead->id,
                'service_id' => $serviceId,
                'name' => $cost->name,
                'amount' => $cost->amount,
                'billing_type' => $cost->billing_type,
            ]);
        }
    }

    // ✅ Redirect to lead show page
    return redirect()->route('leads.index', $lead->id)
        ->with('success', 'New service request created successfully as a Lead.');
}
public function saveProfile(Request $request, Customer $customer)
{
    $data = [];

    foreach ($request->input('status', []) as $section => $subpoints) {
        foreach ($subpoints as $subpoint => $statuses) {
            $filePath = null;
            if ($request->hasFile("file.$section.$subpoint")) {
                $filePath = $request->file("file.$section.$subpoint")->store('proofs', 'public');
            }

            $data[$section][$subpoint] = [
                'planning' => isset($statuses['planning']),
                'documentation' => isset($statuses['documentation']),
                'training' => isset($statuses['training']),
                'file' => $filePath,
                'remarks' => $request->input("remarks.$section.$subpoint", null),
            ];
        }
    }

    $customer->update(['business_mentoring_profile' => json_encode($data)]);

    return back()->with('success', 'Profile updated successfully!');
}



    // Soft delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
