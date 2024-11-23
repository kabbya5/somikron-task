<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusOptions = ['Present', 'Absent', 'Leave'];
        $status = $statusOptions[array_rand($statusOptions)];

        $data = [
            'employee_id'  => Employee::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => $status,
        ];

        if ($status == 'Present') {
            $data['present_at'] = $this->faker->randomFloat(2, 9.10, 10.00);
            $data['leave_at'] = $this->faker->randomFloat(2, 5.30, 6.00);
        }

        return $data;
    }
}
