<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Followup;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\Reminder;
use Illuminate\Support\Facades\Log;
use App\Models\LeadService;
use App\Models\Customer;
use App\Models\CustomerService;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ServiceCost;
use App\Models\LeadServiceCost;
use App\Models\CustomerServiceCost;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;




class LeadController extends Controller
{
    
public function getData(Request $request)
{
    $query = Lead::with(['services', 'followups']);

    // 🔍 Apply filters
    if ($request->search) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('source', 'like', "%{$search}%")
              ->orWhereHas('services', function ($serviceQuery) use ($search) {
                  $serviceQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($request->status) {
        $query->where('status', $request->status);
    }

    if ($request->source) {
        $query->where('source', $request->source);
    }

    if ($request->month) {
        $month = Carbon::parse($request->month)->month;
        $year = Carbon::parse($request->month)->year;
        $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('services', function ($lead) {
            return $lead->services->map(function ($service) {
                return '<span class="badge bg-info">'.\App\Helpers\StringHelper::abbreviate($service->name).'</span>';
            })->implode(' ');
        })
        ->addColumn('last_followup', function ($lead) {
            return optional($lead->followups->last()?->next_followup_date)->format('d-m-Y H:i') ?? '—';
        })
        ->addColumn('actions', function ($lead) {
            return view('partials.datatables.actions', [
                'viewRoute' => route('leads.show', $lead->id),
                'editRoute' => route('leads.edit', $lead->id),
                'deleteRoute' => null // leads have no delete
            ])->render();
        })
        ->rawColumns(['services', 'actions'])
        ->make(true);
}

public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $source = $request->input('source');
    $month = $request->input('month'); // e.g. "2025-10"

    $leads = Lead::with('services', 'followups')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhereHas('services', function ($serviceQuery) use ($search) {
                      $serviceQuery->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->when($status, function ($query, $status) {
            $query->where('status', $status);
        })
        ->when($source, function ($query, $source) {
            $query->where('source', $source);
        })
        ->when($month, function ($query, $month) {
            [$year, $monthNum] = explode('-', $month);
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $monthNum);
        })
        ->latest()
        ->paginate(10);

    return view('admin.leads.index', compact('leads', 'search', 'status', 'source', 'month'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //  dd(setting('Lead', 'Statuses'));

       $services = Service::where('is_active', true)->get();
        return view('admin.leads.create', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // 🔹 Step 1: Validate input
    $request->validate([
        'company_name' => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'designation' => 'nullable|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email',
        'services' => 'required|array',
        'source' => 'nullable|string',
        'source_sub' => 'nullable|string',
    ]);

    // 🔹 Step 2: Create Lead
    $leadCode = Lead::generateLeadCode();
    $lead = Lead::create([
        'lead_code' => $leadCode,
        'company_name' => $request->company_name,
        'contact_person' => $request->contact_person,
        'designation' => $request->designation,
        'phone' => $request->phone,
        'email' => $request->email,
        'status' => $request->status ?? 'New',
        'source' => $request->source,
        'source_sub' => $request->source_sub,
        'created_by' => Auth::id(),
        'address_line1' => $request->address_line1,
        'address_line2' => $request->address_line2,
        'country' => $request->country,
        'state' => $request->state,
        'district' => $request->district,
        'city' => $request->city,
        'pincode' => $request->pincode,
    ]);

    //Attach selected services
    $lead->services()->sync($request->services);

    //  Handle cost saving
    // The Blade form sends cost_name[service_id][], cost_amount[service_id][], cost_billing_type[service_id][]
    $hasEditedCosts = $request->has('cost_name');

    if ($hasEditedCosts) {
        // ✅ Use the edited costs from the form
        foreach ($request->services as $serviceId) {
            if (isset($request->cost_name[$serviceId])) {
                foreach ($request->cost_name[$serviceId] as $index => $costName) {
                    $amount = $request->cost_amount[$serviceId][$index] ?? 0;
                    $billingType = $request->cost_billing_type[$serviceId][$index] ?? 'one_time';

                    // Save under the corresponding lead-service relation
                    LeadServiceCost::create([
                        'lead_id' => $lead->id,
                        'service_id' => $serviceId,
                        'name' => $costName,
                        'amount' => $amount,
                        'billing_type' => $billingType,
                    ]);
                }
            }
        }
    } else {
        //  If no manual edits, copy default service costs
        foreach ($lead->services as $service) {
            $serviceCosts = ServiceCost::where('service_id', $service->id)->get();

            foreach ($serviceCosts as $sc) {
                LeadServiceCost::create([
                    'lead_id' => $lead->id,
                    'service_id' => $service->id,
                    'name' => $sc->name,
                    'amount' => $sc->amount,
                    'billing_type' => $sc->billing_type,
                ]);
            }
        }
    }

    //  Handle follow-up and reminder
    if ($request->filled('next_followup_date')) {
        Followup::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'notes' => $request->notes,
            'next_followup_date' => $request->next_followup_date,
        ]);

        Reminder::create([
            'title' => "Follow-up with {$lead->company_name}",
            'description' => $request->notes,
            'remind_at' => $request->next_followup_date,
            'type' => 'lead_followup',
            'related_id' => $lead->id,
        ]);
    }

    //  Redirect
    return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $staff = User::all();  
       $lead = Lead::with(['serviceCosts.service', 'followups.staff'])->findOrFail($lead->id);


        return view('admin.leads.show', compact('lead','staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
         if (strtolower($lead->status) === 'converted') {
        return redirect()->route('leads.show', $lead->id)
            ->with('error', 'This lead has been converted to a customer and cannot be edited.');
    }
       $services = Service::where('is_active', true)->get();
         $lead->load('serviceCosts'); // eager load
        return view('admin.leads.edit', compact('lead', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    
public function update(Request $request, Lead $lead)
{
    $request->validate([
        'company_name' => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'services' => 'required|array',
    ]);

    // 🔹 Update basic info
    $lead->update([
        'company_name' => $request->company_name,
        'contact_person' => $request->contact_person,
        'designation' => $request->designation,
        'phone' => $request->phone,
        'email' => $request->email,
        'status' => $request->status,
        'address_line1' => $request->address_line1,
        'address_line2' => $request->address_line2,
        'country' => $request->country,
        'state' => $request->state,
        'district' => $request->district,
        'city' => $request->city,
        'pincode' => $request->pincode,
    ]);

    // 🔹 Update service relations
    $lead->services()->sync($request->services);

    // 🔹 Remove old cost entries and save new ones
    LeadServiceCost::where('lead_id', $lead->id)->delete();

    foreach ($request->services as $serviceId) {
        if (isset($request->cost_name[$serviceId])) {
            foreach ($request->cost_name[$serviceId] as $index => $name) {
                LeadServiceCost::create([
                    'lead_id' => $lead->id,
                    'service_id' => $serviceId,
                    'name' => $name,
                    'amount' => $request->cost_amount[$serviceId][$index] ?? 0,
                    'billing_type' => $request->cost_billing_type[$serviceId][$index] ?? 'one_time',
                ]);
            }
        }
    }

    // 🔹 Handle follow-up (if new one added)
    if ($request->filled('next_followup_date')) {
        Followup::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'notes' => $request->notes,
            'next_followup_date' => $request->next_followup_date,
        ]);

        Reminder::create([
            'title' => "Follow-up with {$lead->company_name}",
            'description' => $request->notes,
            'remind_at' => $request->next_followup_date,
            'type' => 'lead_followup',
            'related_id' => $lead->id,
        ]);
    }

    return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

/*public function convertToCustomer($id)
{
    $lead = Lead::with(['services', 'serviceCosts'])->findOrFail($id);

    // Prevent double conversion
    if (strtolower($lead->status) === 'converted') {
        return redirect()->route('customers.index')
            ->with('info', 'Lead already converted.');
    }

    // If lead already linked to customer → update existing
    if ($lead->customer_id) {

        $customer = Customer::find($lead->customer_id);

        if ($customer) {

            $customer->update([
                'company_name'    => $lead->company_name,
                'contact_person'  => $lead->contact_person,
                'phone'           => $lead->phone,
                'email'           => $lead->email,
                'address_line1'   => $lead->address_line1,
                'address_line2'   => $lead->address_line2,
                'country'         => $lead->country,
                'state'           => $lead->state,
                'district'        => $lead->district,
                'city'            => $lead->city,
                'pincode'         => $lead->pincode,
            ]);

            // Attach missing services
            foreach ($lead->services as $service) {
                if (!$customer->services->contains($service->id)) {
                    $serviceCode = CustomerService::generateServiceCode($service->name);
                    $customer->services()->attach($service->id, ['service_code' => $serviceCode]);
                }
            }

            // Update service costs
            foreach ($lead->serviceCosts as $cost) {
                CustomerServiceCost::updateOrCreate([
                    'customer_id' => $customer->id,
                    'service_id'  => $cost->service_id,
                    'name'        => $cost->name,
                ], [
                    'quoted_amount'   => $cost->amount,
                    'billing_type'    => $cost->billing_type,
                    'approved_amount' => null,
                ]);
            }

            // Link project → customer
            Project::where('lead_id', $lead->id)->update([
                'customer_id' => $customer->id
            ]);

            $lead->update(['status' => 'Converted']);

            return redirect()->route('customers.index')
                ->with('success', 'Existing customer updated with new services.');
        }
    }

    // Otherwise → create new customer
    $customer = Customer::create([
        'customer_code'   => Customer::generateCustomerCode(),
        'company_name'    => $lead->company_name,
        'contact_person'  => $lead->contact_person,
        'phone'           => $lead->phone,
        'email'           => $lead->email,
        'address_line1'   => $lead->address_line1,
        'address_line2'   => $lead->address_line2,
        'country'         => $lead->country,
        'state'           => $lead->state,
        'district'        => $lead->district,
        'city'            => $lead->city,
        'pincode'         => $lead->pincode,
        'lead_id'         => $lead->id,
    ]);

    // Attach services with codes
    foreach ($lead->services as $service) {
        $serviceCode = CustomerService::generateServiceCode($service->name);
        $customer->services()->attach($service->id, ['service_code' => $serviceCode]);
    }

    // Copy service costs
    foreach ($lead->serviceCosts as $cost) {
        CustomerServiceCost::create([
            'customer_id'     => $customer->id,
            'service_id'      => $cost->service_id,
            'name'            => $cost->name,
            'quoted_amount'   => $cost->amount,
            'approved_amount' => null,
            'billing_type'    => $cost->billing_type,
        ]);
    }

    // LINK PROJECT → NEW CUSTOMER
    Project::where('lead_id', $lead->id)->update([
        'customer_id' => $customer->id
    ]);

    // Update lead
    $lead->update([
        'status'      => 'Converted',
        'customer_id' => $customer->id,
    ]);

    return redirect()->route('customers.index')
        ->with('success', "Lead converted to new customer successfully: {$customer->company_name}");
}*/
public function convertToCustomer($id)
{
    $lead = Lead::with(['services', 'serviceCosts'])->findOrFail($id);

    // Prevent double conversion
    if (strtolower($lead->status) === 'converted') {
        return redirect()->route('customers.index')->with('info', 'Lead already converted.');
    }

    // ============================================
    // 🔹 CASE 1: Lead already linked to a customer
    // ============================================
    if ($lead->customer_id) {

        $customer = Customer::with(['services'])->find($lead->customer_id);

        if ($customer) {

            // 🔸 Update customer fields from lead
            $customer->update([
                'company_name'   => $lead->company_name,
                'contact_person' => $lead->contact_person,
                'phone'          => $lead->phone,
                'email'          => $lead->email,
                'address_line1'  => $lead->address_line1,
                'address_line2'  => $lead->address_line2,
                'country'        => $lead->country,
                'state'          => $lead->state,
                'district'       => $lead->district,
                'city'           => $lead->city,
                'pincode'        => $lead->pincode,
            ]);

            // 🔸 Attach NEW services (avoid duplicates)
            foreach ($lead->services as $service) {
                if (!$customer->services->contains($service->id)) {

                    $serviceCode = CustomerService::generateServiceCode($service->name);

                    $customer->services()->attach($service->id, [
                        'service_code' => $serviceCode
                    ]);
                }
            }

            // 🔸 Update service costs
            foreach ($lead->serviceCosts as $cost) {
                CustomerServiceCost::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'service_id'  => $cost->service_id,
                        'name'        => $cost->name,
                    ],
                    [
                        'quoted_amount'   => $cost->amount,
                        'billing_type'    => $cost->billing_type,
                        'approved_amount' => null,
                    ]
                );
            }

            // 🔸 Mark lead as converted
            $lead->update(['status' => 'Converted']);

            // 🔥 UPDATE EXISTING PROJECTS CREATED BEFORE CONVERSION
            Project::where('lead_id', $lead->id)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);

            return redirect()
                ->route('customers.index')
                ->with('success', 'Existing customer updated with new services.');
        }
    }

    // ============================================
    // 🔹 CASE 2: Create NEW customer
    // ============================================
    $customer = Customer::create([
        'customer_code'   => Customer::generateCustomerCode(), 
        'company_name'    => $lead->company_name,
        'contact_person'  => $lead->contact_person,
        'phone'           => $lead->phone,
        'email'           => $lead->email,
        'address_line1'   => $lead->address_line1,
        'address_line2'   => $lead->address_line2,
        'country'         => $lead->country,
        'state'           => $lead->state,
        'district'        => $lead->district,
        'city'            => $lead->city,
        'pincode'         => $lead->pincode,
        'lead_id'         => $lead->id,
        'created_by'      => Auth::id(),
    ]);

    // Attach all services with service codes
    foreach ($lead->services as $service) {
        $serviceCode = CustomerService::generateServiceCode($service->name);

        $customer->services()->attach($service->id, [
            'service_code' => $serviceCode
        ]);
    }

    // Copy service costs
    foreach ($lead->serviceCosts as $cost) {
        CustomerServiceCost::create([
            'customer_id'     => $customer->id,
            'service_id'      => $cost->service_id,
            'name'            => $cost->name,
            'quoted_amount'   => $cost->amount,
            'approved_amount' => null,
            'billing_type'    => $cost->billing_type,
        ]);
    }

    // Update lead
    $lead->update([
        'status'      => 'Converted',
        'customer_id' => $customer->id,
    ]);

    // 🔥 UPDATE EXISTING PROJECTS CREATED BEFORE CONVERSION
    Project::where('lead_id', $lead->id)
        ->whereNull('customer_id')
        ->update(['customer_id' => $customer->id]);

    return redirect()
        ->route('customers.index')
        ->with('success', "Lead converted to new customer successfully: {$customer->company_name}");
}




/**
 * Return service costs (ServiceCost) for given service IDs (AJAX).
 * Response grouped by service_id, with service name attached.
 */
public function fetchServiceCosts(Request $request)
{
    $serviceIds = $request->input('service_ids', []);
    if (!is_array($serviceIds)) {
        $serviceIds = explode(',', $serviceIds);
    }

    $serviceCosts = ServiceCost::whereIn('service_id', $serviceIds)
                    ->get()
                    ->groupBy('service_id');

    // We also want the service name per id
    $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

    $payload = [];
    foreach ($serviceCosts as $serviceId => $costs) {
        $payload[$serviceId] = [
            'service' => $services[$serviceId] ?? null,
            'costs' => $costs->map(function($c){
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'amount' => (float)$c->amount,
                    'billing_type' => $c->billing_type,
                ];
            })->values(),
        ];
    }

    return response()->json($payload);
}
protected function saveLeadServiceCosts(Lead $lead, array $costPayload)
{
    // costPayload => keyed by service_id: array of cost objects with keys name, amount, billing_type
    // We'll delete existing and re-insert for simplicity
    LeadServiceCost::where('lead_id', $lead->id)->delete();

    foreach ($costPayload as $serviceId => $rows) {
        foreach ($rows as $row) {
            // Basic validation / cast
            $name = $row['name'] ?? null;
            $amount = is_numeric($row['amount'] ?? null) ? (float)$row['amount'] : 0;
            $billingType = $row['billing_type'] ?? null;

            if (!$name) continue;

            LeadServiceCost::create([
                'lead_id' => $lead->id,
                'service_id' => $serviceId,
                'name' => $name,
                'amount' => $amount,
                'billing_type' => $billingType,
            ]);
        }
    }
}
public function getServiceCosts($leadId, $serviceId)
{
    // Try to fetch existing costs for this lead & service
    $existingCosts = LeadServiceCost::where('lead_id', $leadId)
        ->where('service_id', $serviceId)
        ->get(['name', 'amount', 'billing_type']);

    // If none exist (newly added service), fall back to default ServiceCost
    if ($existingCosts->isEmpty()) {
        $existingCosts = ServiceCost::where('service_id', $serviceId)
            ->get(['name', 'amount', 'billing_type']);
    }

    return response()->json($existingCosts);
}



}
