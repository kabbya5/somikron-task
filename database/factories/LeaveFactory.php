<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Leave>
 */
class LeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $startDate = $this->faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d');
        // $endDate = $this->faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d');
        // $days = $startDate->diffInDays($endDate);

        return [
            'employee_id' => Employee::inRandomOrder()->first()->id,
            'start_date' => $this->faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d'),  // Random start date within the last month
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),  // Random end date within the next month
            'type' => $this->faker->randomElement(['Sick Leave', 'Vacation', 'Casual Leave', 'Maternity Leave']),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected']),
            'reason' => $this->faker->text(100),
            'days' => $this->faker->numberBetween(1, 10),
        ];
    }
}
