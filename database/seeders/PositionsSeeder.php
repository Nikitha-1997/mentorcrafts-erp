<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Department;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Business Mentoring' => ['Business Mentor','Mentoring Coordinator'],
            'Software Development' => ['Software Developer','Senior Developer','Tech Lead'],
            'Website Development' => ['Frontend Developer','Backend Developer','Full Stack Developer'],
            'Digital Marketing' => ['SEO Specialist','Social Media Manager','Graphic Designer'],
            'HR' => ['HR Executive','HR Manager'],
            'Accounts' => ['Accountant','Senior Accountant'],
            'Management' => ['Manager','Assistant Manager'],
            'Sales' => ['Sales Executive','Sales Manager'],
        ];

        foreach($positions as $deptName => $posArr){
            $department = Department::where('name',$deptName)->first();
            if($department){
                foreach($posArr as $title){
                    Position::firstOrCreate([
                        'department_id'=>$department->id,
                        'title'=>$title
                    ]);
                }
            }
        }
    }
}
