<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;






class ProjectController extends Controller
{
/*public function createFromLead(Request $request, $leadId)
   
{
    $lead = Lead::findOrFail($leadId);
    // 🔹 Fetch the customer linked to this lead
    $customer = Customer::where('lead_id', $lead->id)->first();


    $request->validate([
        'project_names.*'   => 'required|string|max:255',
        'descriptions.*'    => 'nullable|string',
        'assigned_to.*'     => 'required|exists:users,id',
        'start_date.*'      => 'nullable|date',
        'end_date.*'        => 'nullable|date|after_or_equal:start_date.*',
    ]);

    $projectsCreated = [];

    foreach ($request->project_names as $serviceId => $name) {

        $project = Project::create([
            'lead_id'       => $lead->id,
            'customer_id'   => $customer->id,   // ✅ ADD CUSTOMER ID HERE
            'service_id'    => $serviceId,     // optional if needed
            'project_name'  => $name,
            'description'   => $request->descriptions[$serviceId] ?? null,
            'assigned_to'   => $request->assigned_to[$serviceId] ?? null,
            'start_date'    => $request->start_date[$serviceId] ?? null,
            'end_date'      => $request->end_date[$serviceId] ?? null,
        ]);

        $projectsCreated[] = $project->id;
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Projects created successfully.',
        'project_ids' => $projectsCreated
    ]);
}*/
public function createFromLead(Request $request, Lead $lead)
{
    $request->validate([
        'project_names.*'   => 'required|string|max:255',
        'short_descriptions.*' => 'nullable|string|max:255',
        'descriptions.*'    => 'nullable|string',
        'assigned_to.*'     => 'required|exists:users,id',
        'start_date.*'      => 'nullable|date',
        'end_date.*'        => 'nullable|date|after_or_equal:start_date.*',
    ]);

    // ---------------------------------------------------------
    // GET THE CUSTOMER ID CONSISTENTLY WITH convertToCustomer()
    // ---------------------------------------------------------

    $customerId = null;

    // Case 1: Lead already linked to a customer
    if ($lead->customer_id) {
        $customerId = $lead->customer_id;
    }

    // Case 2: Lead is converted but customer_id not stored yet
    if (!$customerId && strtolower($lead->status) === 'converted') {
        // Try to find customer by lead_id
        $existingCustomer = Customer::where('lead_id', $lead->id)->first();
        if ($existingCustomer) {
            $customerId = $existingCustomer->id;
        }
    }

    // Case 3: Lead is NOT converted yet → Customer does not exist
    // In this case customer_id should remain NULL now
    // It will be updated later after conversion

    $projectsCreated = [];

    foreach ($request->project_names as $serviceId => $name) {
        // -------------------------------------------
        //  GET SERVICE TYPE
        // -------------------------------------------
        $service = Service::find($serviceId);

        // If service exists → use slug of its name
        if ($service) {
            $serviceType = Str::slug($service->name);  
        } else {
            // Fallback: derive from project name
            $serviceType = Str::slug($name);
        }

        $project = Project::create([
            'lead_id'       => $lead->id,
            'customer_id'   => $customerId,  // 🔥 AUTO-FILLED when customer exists
            'service_id'    => $serviceId,
            'project_name'  => $name,
            'service_type'  => $serviceType,
            'short_description' => $request->short_descriptions[$serviceId] ?? null,
            'description'   => $request->descriptions[$serviceId] ?? null,
            'assigned_to'   => $request->assigned_to[$serviceId] ?? null,
            'start_date'    => $request->start_date[$serviceId] ?? null,
            'end_date'      => $request->end_date[$serviceId] ?? null,
        ]);

        $projectsCreated[] = $project->id;
    }

    return response()->json([
        'status'      => 'success',
        'message'     => 'Projects created successfully.',
        'project_ids' => $projectsCreated
    ]);
}
public function manage(Project $project)
{
    $detail = $project->detail;            // null or record
    //dd($detail);
    $serviceType = $project->service_type; // website-development, business-mentoring etc.

    $customers = Customer::all();
    $domainTypes = setting('Projects', 'Domain', []);
    $hostingTypes = setting('Projects', 'Hosting', []);




    // Decide folder based on service
    switch ($serviceType) {

        case 'website-development':
            return view(
                $detail ? 'admin.project_details.website.show' : 'admin.project_details.website.create',
                compact('project', 'detail', 'customers', 'domainTypes', 'hostingTypes')
            );

        case 'business-mentoring':
            return view(
                $detail ? 'admin.project_details.mentoring.show' : 'admin.project_details.mentoring.create',
                compact('project', 'detail', 'customers', 'domainTypes', 'hostingTypes')
            );

        case 'branding':
            return view(
                $detail ? 'admin.project_details.branding.show' : 'admin.project_details.branding.create',
                compact('project', 'detail', 'customers', 'domainTypes', 'hostingTypes')
            );
             case 'web-app-development':
            return view(
                $detail ? 'admin.project_details.webapp.show' : 'admin.project_details.webapp.create',
                compact('project', 'detail', 'customers', 'domainTypes', 'hostingTypes')
            );

        default:
            return view(
                $detail ? 'admin.project_details.default.show' : 'admin.project_details.default.create',
                compact('project', 'detail', 'customers', 'domainTypes', 'hostingTypes')
            );
    }
}



}
