<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $services = [
            ['name' => 'Website Development', 'description' => 'Full-stack web design and development services.'],
            ['name' => 'Business Mentoring', 'description' => 'Strategic business planning and consultancy.'],
            ['name' => 'Software Implementation', 'description' => 'ERP, CRM, and automation software integration.'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
