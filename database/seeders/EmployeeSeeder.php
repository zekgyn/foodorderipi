<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::create([
            'name' => 'Gabriel Mbeke',
            'phone' => 255686260773
        ]);
        Employee::create([
            'name' => 'Mac Issac',
            'phone' => 255686260772
        ]);
        Employee::create([
            'name' => 'Asha Wangu',
            'phone' => 255686260771
        ]);
        Employee::create([
            'name' => 'Juma Yunus',
            'phone' => 255686260770
        ]);
    }
}
