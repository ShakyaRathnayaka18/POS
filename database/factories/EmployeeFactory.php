<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_number' => 'EMP-'.fake()->unique()->numberBetween(1000, 9999),
            'hire_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'employment_type' => fake()->randomElement(['hourly', 'salaried']),
            'hourly_rate' => fake()->randomFloat(2, 15, 50),
            'base_salary' => fake()->randomFloat(2, 30000, 100000),
            'pay_frequency' => 'monthly',
            'department' => fake()->randomElement(['Sales', 'Operations', 'Administration', 'IT']),
            'position' => fake()->jobTitle(),
            'status' => 'active',
        ];
    }
}
