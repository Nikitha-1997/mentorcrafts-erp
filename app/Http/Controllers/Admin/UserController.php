<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmployeeDetail;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreEmployeeRequest;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function index()
    {
        $users = User::with('employeeDetail.department', 'employeeDetail.position')->get();
        return view('admin.users.index', compact('users'));
    }*/
        public function index(Request $request)
{
    if ($request->ajax()) {
        $users = User::with(['employeeDetail.department', 'employeeDetail.position'])->select('users.*');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('employee_id', function ($row) {
                return $row->employeeDetail?->employee_id ?? '—';
            })
            ->addColumn('department', function ($row) {
                return $row->employeeDetail?->department?->name ?? '—';
            })
            ->addColumn('position', function ($row) {
                return $row->employeeDetail?->position?->title ?? '—';
            })
            ->addColumn('actions', function ($row) {
                /** @var \App\Models\User $user */
$user = Auth::user();
if ($user && $user->hasRole(['Super Admin', 'HR'])) {
            $edit = '<a href="' . route('users.edit', $row->id) . '" class="text-primary me-2">
                    <i class="ri-edit-2-line"></i>
                 </a>';

        $delete = '<form action="' . route('users.destroy', $row->id) . '" method="POST" class="d-inline">'
                . csrf_field() . method_field('DELETE') .
                '<button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm(\'Delete employee?\')">
                    <i class="ri-delete-bin-6-line"></i>
                </button></form>';

        return $edit . $delete;
    }

    return '—';
})

            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('admin.users.index');
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        $roles = Role::with('permissions')->get();

        return view('admin.users.create', compact('departments', 'positions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        // Assign role by name
        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        $employeeId = 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $photoPath = $request->file('photo')?->store('uploads/photos', 'public');
        $kycPath = $request->file('kyc_document')?->store('uploads/kyc', 'public');

        EmployeeDetail::create([
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'employee_id' => $employeeId,
            'photo' => $photoPath,
            'kyc_document' => $kycPath,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
            'next_increment_date' => $request->next_increment_date,
            'relieving_date' => $request->relieving_date,
        ]);

        return redirect()->route('users.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = Department::all();
        $positions = Position::all();
        $roles = Role::with('permissions')->get();
        $user->load('roles', 'employeeDetail');

        return view('admin.users.edit', compact('user', 'departments', 'positions', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'role' => 'required|string|exists:roles,name',
            'salary' => 'nullable|numeric',
            'joining_date' => 'nullable|date',
            'next_increment_date' => 'nullable|date',
            'relieving_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kyc_document' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // Update user info
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();

        // Update or create employee details
        $employeeDetail = $user->employeeDetail ?? new EmployeeDetail(['user_id' => $user->id]);

        $employeeDetail->department_id = $validatedData['department_id'];
        $employeeDetail->position_id = $validatedData['position_id'];
        $employeeDetail->salary = $validatedData['salary'] ?? null;
        $employeeDetail->joining_date = $validatedData['joining_date'] ?? null;
        $employeeDetail->next_increment_date = $validatedData['next_increment_date'] ?? null;
        $employeeDetail->relieving_date = $validatedData['relieving_date'] ?? null;

        // Photo upload and replace old file if exists
        if ($request->hasFile('photo')) {
            if ($employeeDetail->photo && file_exists(storage_path('app/public/' . $employeeDetail->photo))) {
                unlink(storage_path('app/public/' . $employeeDetail->photo));
            }
            $employeeDetail->photo = $request->file('photo')->store('uploads/photos', 'public');
        }

        // KYC document upload and replace old file if exists
        if ($request->hasFile('kyc_document')) {
            if ($employeeDetail->kyc_document && file_exists(storage_path('app/public/' . $employeeDetail->kyc_document))) {
                unlink(storage_path('app/public/' . $employeeDetail->kyc_document));
            }
            $employeeDetail->kyc_document = $request->file('kyc_document')->store('uploads/kyc', 'public');
        }

        $employeeDetail->save();

        // Sync single role
        $user->syncRoles([$validatedData['role']]);

        return redirect()->route('users.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Delete related employee details & files
        if ($user->employeeDetail) {
            if ($user->employeeDetail->photo && file_exists(storage_path('app/public/' . $user->employeeDetail->photo))) {
                unlink(storage_path('app/public/' . $user->employeeDetail->photo));
            }
            if ($user->employeeDetail->kyc_document && file_exists(storage_path('app/public/' . $user->employeeDetail->kyc_document))) {
                unlink(storage_path('app/public/' . $user->employeeDetail->kyc_document));
            }
            $user->employeeDetail->delete();
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Employee deleted successfully.');
    }
}

