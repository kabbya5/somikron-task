<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Promotion>
 */
class PromotionFactory extends Factory
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
            'promotion_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),  // Random promotion date within the last 2 years
            'new_position' => $this->faker->word,
            'salary_increment' => $this->faker->numberBetween(5000, 20000),
        ];
    }
}
