<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Business Mentoring','Software Development','Website Development','Digital Marketing',
            'HR','Accounts','Management','Sales',
        ];
        foreach($departments as $dept){
            Department::firstOrCreate(['name'=>$dept]);
        }
    }
}
