<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerProjectDetail;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class CustomerProjectDetailController extends Controller
{
    public function index()
    {
        $details = CustomerProjectDetail::with('customer')->latest()->get();
        return view('admin.customer_project_details.index', compact('details'));
    }

    public function create()
    {
        $customers = Customer::all();

        // ✅ Fetch domain & hosting types from settings helper
        $domainTypes = setting('Projects', 'Domain', []);
        
        $hostingTypes = setting('Projects', 'Hosting', []);

        return view('admin.customer_project_details.create', compact('customers', 'domainTypes', 'hostingTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'customer_id' => 'required|exists:customers,id',
            'project_name' => 'nullable|string|max:255',

            // Domain Fields
            'domain_type' => 'nullable|string|max:255',
            'domain_service_provider' => 'nullable|string|max:255',
            'domain_purchase_date' => 'nullable|date',
            'domain_expiry_date' => 'nullable|date',
            'domain_subscription_duration' => 'nullable|string|max:255',
            'domain_username' => 'nullable|string|max:255',
            'domain_password' => 'nullable|string|max:255',
            'domain_url' => 'nullable|string|max:255',
            'domain_not_included_in_amc' => 'boolean',

            // Hosting Fields
            'hosting_type' => 'nullable|string|max:255',
            'hosting_service_provider' => 'nullable|string|max:255',
            'hosting_purchase_date' => 'nullable|date',
            'hosting_expiry_date' => 'nullable|date',
            'hosting_username' => 'nullable|string|max:255',
            'hosting_password' => 'nullable|string|max:255',
            'hosting_url' => 'nullable|string|max:255',
            'hosting_not_included_amc' => 'boolean',

            //Amc Fileds
            'amc_description' => 'nullable|string|max:255',
            'amc_month' => 'nullable|integer',
            'amc_amount' => 'nullable|numeric',
            'amc_remarks' => 'nullable|string',

            // Other Fields
            'ssl_provider' => 'nullable|string|max:255',
            'cms_or_technology' => 'nullable|string|max:255',
            'server_details' => 'nullable|string',
            'credentials' => 'nullable|string',
            'notes' => 'nullable|string',
            
        ]);

    $validated['domain_not_included_amc'] = $request->has('domain_not_included_amc');

        // ✅ Create the record
        $project = CustomerProjectDetail::create($validated);

        // ✅ Domain renewal reminder
        if (!empty($validated['domain_expiry_date'])) {
            $remindDate = Carbon::parse($validated['domain_expiry_date'])->subDays(7);

            Reminder::create([
                'title' => 'Domain Expiry Reminder',
                'description' => 'The domain for project "' . ($validated['project_name'] ?? 'Unnamed Project') . '" will expire soon.',
                'remind_at' => $remindDate,
                'type' => 'domain_expiry',
                'related_id' => $project->id,
            ]);
        }

        // ✅ Hosting renewal reminder
        if (!empty($validated['hosting_expiry_date'])) {
            $remindDate = Carbon::parse($validated['hosting_expiry_date'])->subDays(7);

            Reminder::create([
                'title' => 'Hosting Expiry Reminder',
                'description' => 'The hosting for project "' . ($validated['project_name'] ?? 'Unnamed Project') . '" will expire soon.',
                'remind_at' => $remindDate,
                'type' => 'hosting_expiry',
                'related_id' => $project->id,
            ]);
        }

        return redirect()->route('customer-project-details.index')
            ->with('success', 'Customer project detail added successfully and reminders set.');
    }
        public function show($id)
        {
            $detail = CustomerProjectDetail::with('customer')->findOrFail($id);
            dd($detail);

            return view('admin.project_details.webapp.show', compact('detail'));
        }


    public function edit(CustomerProjectDetail $customer_project_detail)
    {
        
        $customers = Customer::all();
        $domainTypes = setting('Projects', 'Domain', []);
        $hostingTypes = setting('Projects', 'Hosting', []);

        return view('admin.customer_project_details.edit', [
            'detail' => $customer_project_detail,
            'customers' => $customers,
            'domainTypes' => $domainTypes,
            'hostingTypes' => $hostingTypes,
        ]);
    }

    public function update(Request $request, CustomerProjectDetail $customerProjectDetail)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'project_name' => 'nullable|string|max:255',

            // Domain Fields
            'domain_type' => 'nullable|string|max:255',
            'domain_service_provider' => 'nullable|string|max:255',
            'domain_purchase_date' => 'nullable|date',
            'domain_expiry_date' => 'nullable|date',
            'domain_subscription_duration' => 'nullable|string|max:255',
            'domain_username' => 'nullable|string|max:255',
            'domain_password' => 'nullable|string|max:255',
            'domain_url' => 'nullable|string|max:255',
            'domain_not_included_in_amc' => 'boolean',

            // Hosting Fields
            'hosting_type' => 'nullable|string|max:255',
            'hosting_service_provider' => 'nullable|string|max:255',
            'hosting_purchase_date' => 'nullable|date',
            'hosting_expiry_date' => 'nullable|date',
            'hosting_subscription_duration' => 'nullable|string|max:255',
            'hosting_username' => 'nullable|string|max:255',
            'hosting_password' => 'nullable|string|max:255',
            'hosting_url' => 'nullable|string|max:255',
            'hosting_not_included_amc' => 'boolean',

            //AMC Fields
            'amc_description' => 'nullable|string|max:255',
            'amc_month' => 'nullable|integer',
            'amc_amount' => 'nullable|numeric',
            'amc_remarks' => 'nullable|string',

            // Other Fields
            'ssl_provider' => 'nullable|string|max:255',
            'cms_or_technology' => 'nullable|string|max:255',
            'server_details' => 'nullable|string',
            'credentials' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    $validated['domain_not_included_in_amc'] = $request->has('domain_not_included_in_amc');

        // ✅ Update main record
        $customerProjectDetail->update($validated);

        // ✅ Update or delete reminders
        $this->updateReminders($customerProjectDetail, $validated);

        return redirect()->route('customer-project-details.index')
            ->with('success', 'Customer project detail updated successfully.');
    }

    protected function updateReminders($project, $validated)
    {
        // Domain Reminder
        if (!empty($validated['domain_expiry_date'])) {
            $remindDate = Carbon::parse($validated['domain_expiry_date'])->subDays(7);

            Reminder::updateOrCreate(
                ['related_id' => $project->id, 'type' => 'domain_expiry'],
                [
                    'title' => 'Domain Expiry Reminder',
                    'description' => 'The domain for project "' . ($validated['project_name'] ?? 'Unnamed Project') . '" will expire soon.',
                    'remind_at' => $remindDate,
                ]
            );
        } else {
            Reminder::where('related_id', $project->id)->where('type', 'domain_expiry')->delete();
        }

        // Hosting Reminder
        if (!empty($validated['hosting_expiry_date'])) {
            $remindDate = Carbon::parse($validated['hosting_expiry_date'])->subDays(7);

            Reminder::updateOrCreate(
                ['related_id' => $project->id, 'type' => 'hosting_expiry'],
                [
                    'title' => 'Hosting Expiry Reminder',
                    'description' => 'The hosting for project "' . ($validated['project_name'] ?? 'Unnamed Project') . '" will expire soon.',
                    'remind_at' => $remindDate,
                ]
            );
        } else {
            Reminder::where('related_id', $project->id)->where('type', 'hosting_expiry')->delete();
        }
    }

    public function destroy(CustomerProjectDetail $customer_project_detail)
    {
        $customer_project_detail->delete();
        Reminder::where('related_id', $customer_project_detail->id)->delete();

        return redirect()->route('customer-project-details.index')
            ->with('success', 'Customer project detail deleted.');
    }
}
