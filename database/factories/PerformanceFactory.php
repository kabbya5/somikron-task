<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Performance>
 */
class PerformanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'review_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'score' => $this->faker->numberBetween(1, 10),
            'comments' => $this->faker->text(150),
            'reviewer' => $this->faker->name,
        ];
    }
}
