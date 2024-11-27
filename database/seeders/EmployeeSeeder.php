<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Performance;
use App\Models\Promotion;
use App\Models\Salary;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee_data = 5000;
        Employee::factory($employee_data)->create()->each(function ($employee) {
            $attendanceCount = rand(1, 5);
            $leaveCount = rand(1, 3);
            $performanceCount = rand(1, 3);
            $promotionCount = rand(0, 3);
            $salaryCount = 1;

            Attendance::factory($attendanceCount)->create([
                'employee_id' => $employee->id,
            ]);

            Leave::factory($leaveCount)->create([
                'employee_id' => $employee->id,
            ]);

            Performance::factory($performanceCount)->create([
                'employee_id' => $employee->id,
            ]);

            Promotion::factory($promotionCount)->create([
                'employee_id' => $employee->id,
            ]);

            Salary::factory($salaryCount)->create([
                'employee_id' => $employee->id,
            ]);
        });

    }
}
