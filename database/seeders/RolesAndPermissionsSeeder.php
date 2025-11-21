<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeDetail;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * Step 1: Ensure Departments and Positions are seeded first
         */
        $this->call([
            DepartmentsSeeder::class,
            PositionsSeeder::class,
        ]);

        /**
         * Step 2: Define modules and permissions
         */
        $modules = [
            'HR Management' => ['view employees', 'manage leave requests', 'manage users', 'manage departments', 'manage positions'],
            'Finance' => ['process payroll', 'view reports'],
            'Inventory' => ['manage items', 'view stock', 'manage services'],
        ];

        /**
         * Step 3: Create roles and assign permissions dynamically
         */
        foreach ($modules as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            foreach ($permissions as $perm) {
                $permission = Permission::firstOrCreate(['name' => $perm]);
                $role->givePermissionTo($permission);
            }
        }

        // Create Super Admin with all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        /**
         * Step 4: Create Employee role with limited permissions
         */
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = Permission::whereIn('name', [
            'view employees',
            'view reports',
            'view stock',
        ])->get();
        $employeeRole->syncPermissions($employeePermissions);

        /**
         * Step 5: Create Super Admin user
         */
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@erp.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // change later
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $superAdminEmployeeId = 'EMP' . str_pad($superAdmin->id, 4, '0', STR_PAD_LEFT);
        $department = Department::where('name', 'Management')->first();
        $position = Position::where('title', 'Manager')->first();

        EmployeeDetail::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'employee_id' => $superAdminEmployeeId,
                'department_id' => $department ? $department->id : null,
                'position_id' => $position ? $position->id : null,
            ]
        );

        /**
         * Step 6: Create a test Employee user
         */
        $employee = User::firstOrCreate(
            ['email' => 'employee@erp.com'],
            [
                'name' => 'Test Employee',
                'password' => Hash::make('password123'),
            ]
        );
        $employee->assignRole('employee');

        $employeeId = 'EMP' . str_pad($employee->id, 4, '0', STR_PAD_LEFT);
        $employeeDept = Department::inRandomOrder()->first();
        $employeePos = Position::inRandomOrder()->first();

        EmployeeDetail::firstOrCreate(
            ['user_id' => $employee->id],
            [
                'employee_id' => $employeeId,
                'department_id' => $employeeDept ? $employeeDept->id : null,
                'position_id' => $employeePos ? $employeePos->id : null,
            ]
        );

        echo "\n✅ Super Admin: superadmin@erp.com | password123";
        echo "\n✅ Employee: employee@erp.com | password123";
        echo "\n✅ Roles and permissions seeded successfully.\n";
    }
}
