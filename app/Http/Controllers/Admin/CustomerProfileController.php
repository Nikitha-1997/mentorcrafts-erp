<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CustomerProfileStatus;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class CustomerProfileController extends Controller
{
    public function show(Customer $customer, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $sections = setting('Service_structure', $service->name, []);

        $statuses = CustomerProfileStatus::where('customer_id', $customer->id)
            ->where('service_name', $service->name)
            ->get()
            ->keyBy(fn($item) => $item->section_name . '|' . $item->subpoint_name);

        $serviceName = Str::slug(strtolower($service->name));
        $viewName = "admin.customers.profiles.{$serviceName}_profile";

        if (view()->exists($viewName)) {
            return view($viewName, compact('customer', 'service', 'sections', 'statuses'));
        }

        return view('admin.customers.profiles.default_profile', compact('customer', 'service', 'sections', 'statuses'));
    }

    public function update(Request $request, Customer $customer, $serviceId)
    {
        $serviceName = $request->service_name;

        DB::beginTransaction();
        try {
            foreach ($request->input('subpoints', []) as $subpointKey => $data) {
                $sectionName = $data['section'] ?? null;
                if (!$sectionName) continue;

                $status = CustomerProfileStatus::firstOrNew([
                    'customer_id' => $customer->id,
                    'service_name' => $serviceName,
                    'section_name' => $sectionName,
                    'subpoint_name' => $subpointKey,
                ]);

                // Checkboxes
                $status->planning = isset($data['planning']) ? 1 : ($status->planning ?? 0);
                $status->documentation = isset($data['documentation']) ? 1 : ($status->documentation ?? 0);
                $status->implementation = isset($data['implementation']) ? 1 : ($status->implementation ?? 0);
                $status->training = isset($data['training']) ? 1 : ($status->training ?? 0);

                // ✅ Remarks - make sure we do NOT double encode
               

                // ✅ Handle multiple file uploads
                $uploadedFiles = [];
                if ($request->hasFile("subpoints.$subpointKey.files")) {
                    foreach ($request->file("subpoints.$subpointKey.files") as $file) {
                        $path = $file->store('uploads/customer_profiles', 'public');
                        $uploadedFiles[] = $path;
                    }
                }

                if (!empty($uploadedFiles)) {
                    $existingFiles = json_decode($status->file_path, true) ?? [];
                    $status->file_path = json_encode(array_merge($existingFiles, $uploadedFiles));
                }

                $status->save();
            }

            DB::commit();
            return back()->with('success', 'Profile updated successfully and retained!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function saveRemark(Request $request)
    {
        $request->validate([
            'subpoint_id' => 'required|integer',
            'remark' => 'required|string|max:1000',
        ]);

        $status = CustomerProfileStatus::find($request->subpoint_id);
        if (!$status) {
            return response()->json(['message' => 'Subpoint not found'], 404);
        }

        $remarkText = trim($request->remark);

        $remarks = json_decode($status->remarks, true) ?? [];

        // ✅ prevent nested JSON remarks
        if (Str::startsWith($remarkText, '[') && Str::endsWith($remarkText, ']')) {
            $remarkText = 'Invalid remark format ignored.';
        }

        $remarks[] = [
            'user' => Auth::user()->name ?? 'System',
            'text' => $remarkText,
            'time' => now()->format('Y-m-d H:i:s'),
        ];

        $status->remarks = json_encode($remarks, JSON_UNESCAPED_UNICODE);
        $status->save();

        return response()->json(['success' => true, 'remarks' => $remarks]);
    }

    public function updateRemark(Request $request)
    {
        $request->validate([
            'subpoint_id' => 'required|integer',
            'index' => 'required|integer',
            'remark' => 'required|string|max:1000',
        ]);

        $status = CustomerProfileStatus::find($request->subpoint_id);
        if (!$status) {
            return response()->json(['message' => 'Subpoint not found'], 404);
        }

        $remarks = json_decode($status->remarks, true) ?? [];
        if (!isset($remarks[$request->index])) {
            return response()->json(['message' => 'Invalid remark index'], 400);
        }

        $remarkText = trim($request->remark);
        if (Str::startsWith($remarkText, '[') && Str::endsWith($remarkText, ']')) {
            $remarkText = 'Invalid remark format ignored.';
        }

        $remarks[$request->index]['text'] = $remarkText;
        $remarks[$request->index]['edited_at'] = now()->format('Y-m-d H:i:s');

        $status->remarks = json_encode($remarks, JSON_UNESCAPED_UNICODE);
        $status->save();

        return response()->json(['success' => true, 'remarks' => $remarks]);
    }
}
