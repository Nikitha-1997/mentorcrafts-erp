<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Yajra\DataTables\Facades\DataTables;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::latest()->paginate(10);
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Fetch settings data for DataTable.
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $settings = Setting::select('id', 'group', 'key', 'value')->orderBy('id', 'asc');

            return DataTables::of($settings)
                ->addIndexColumn()
                ->editColumn('value', function ($setting) {
                    // Decode JSON if needed
                    $values = $setting->value;
                    if (is_string($values)) {
                        $decoded = json_decode($values, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $values = $decoded;
                        }
                    }

                    // Format as HTML badges
                    if (is_array($values)) {
                        $html = '';
                        foreach ($values as $category => $subvalues) {
                            if (is_array($subvalues)) {
                                $html .= '<div class="mb-2"><strong>' . ucfirst(str_replace('_', ' ', $category)) . ':</strong> ';
                                foreach ($subvalues as $sub) {
                                    $html .= '<span class="badge bg-info text-dark me-1">' . e($sub) . '</span>';
                                }
                                $html .= '</div>';
                            } else {
                                $html .= '<span class="badge bg-info text-dark me-1">' . e($subvalues) . '</span>';
                            }
                        }
                        return $html;
                    }

                    return '<span class="text-muted">' . e($values ?: '—') . '</span>';
                })
                ->addColumn('actions', function ($setting) {
                    $edit = '<a href="' . route('settings.edit', $setting->id) . '" class="btn btn-sm btn-warning me-1">
                                <i class="ri-edit-line"></i> Edit
                            </a>';
                    $delete = '
                        <form action="' . route('settings.destroy', $setting->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this setting?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button class="btn btn-sm btn-danger">
                                <i class="ri-delete-bin-line"></i> Delete
                            </button>
                        </form>';

                    return $edit . $delete;
                })
                ->rawColumns(['value', 'actions'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('admin.settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   /* public function store(Request $request)
    {
       $request->validate([
            'group' => 'required|string',
            'key' => 'required|string',
             'categories' => 'required|array|min:1',
        ]);
         $values = [];
        foreach ($request->categories as $category) {
        $values[$category['name']] = $category['sub_values'] ?? [];
        }

        Setting::create([
            'group' => $request->group,
        'key' => $request->key,
        'value' => $values,
        ]);

        return redirect()->route('settings.index')->with('success', 'Setting created successfully.');
    }*/
        public function store(Request $request)
{
    $request->validate([
        'group' => 'required|string',
        'key' => 'required|string',
        'categories' => 'required|array|min:1',
    ]);

    $values = [];
    foreach ($request->categories as $category) {
        if (!empty($category['name'])) {
            $values[$category['name']] = $category['sub_values'] ?? [];
        }
    }

    Setting::create([
        'group' => $request->group,
        'key' => $request->key,
        'value' => $values,
    ]);

    return redirect()->route('settings.index')->with('success', 'Setting created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    /*public function update(Request $request, $id)
    {
         $request->validate([
            'group' => 'required|string',
            'key' => 'required|string',
            'values' => 'required|array|min:1',
        ]);

        $setting = Setting::findOrFail($id);
        $setting->update([
            'group' => $request->group,
            'key' => $request->key,
            'value' => $request->values,
        ]);

        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
    }*/
public function update(Request $request, $id)
{
    $request->validate([
        'group' => 'required|string',
        'key' => 'required|string',
        'categories' => 'required|array|min:1',
    ]);

    $values = [];
    foreach ($request->categories as $category) {
        if (!empty($category['name'])) {
            $values[$category['name']] = $category['sub_values'] ?? [];
        }
    }

    $setting = Setting::findOrFail($id);
    $setting->update([
        'group' => $request->group,
        'key' => $request->key,
        'value' => $values,
    ]);

    return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
          $setting = Setting::findOrFail($id);
        $setting->delete();

        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully.');
    }
}
