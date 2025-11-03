<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;

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

    // Only pass services and existing customer data
    return view('admin.customers.edit', compact('customer', 'services'));
}


    // Show customer details
    public function show(Customer $customer)
    {
        $customer->load('services', 'lead');
        return view('admin.customers.show', compact('customer'));
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

        // Update customer basic + address fields
        $customer->update([
            'company_name'   => $request->company_name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address_line1'  => $request->address_line1,
            'address_line2'  => $request->address_line2,
            'country'        => $request->country,
            'state'          => $request->state,
            'district'       => $request->district,
            'city'           => $request->city,
            'pincode'        => $request->pincode,
        ]);

        // Sync services with pivot data (service_code)
        if ($request->filled('services')) {
            $syncData = [];

            foreach ($request->services as $serviceId) {
                $existing = $customer->services()
                    ->where('service_id', $serviceId)
                    ->first();

                if ($existing) {
                    // Keep existing code
                    $syncData[$serviceId] = ['service_code' => $existing->pivot->service_code];
                } else {
                    // Generate new code
                    $service = Service::find($serviceId);
                    $code = \App\Models\CustomerService::generateServiceCode($service->name);
                    $syncData[$serviceId] = ['service_code' => $code];
                }
            }

            $customer->services()->sync($syncData);
        } else {
            $customer->services()->detach();
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    // Soft delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
