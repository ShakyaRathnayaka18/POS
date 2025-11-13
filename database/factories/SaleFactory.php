<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'sale_number' => 'SALE-'.$this->faker->unique()->numberBetween(1000, 9999),
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'subtotal' => 0, // Will be calculated later
            'tax' => 0, // Will be calculated later
            'total' => 0, // Will be calculated later
            'payment_method' => $this->faker->randomElement(['Cash', 'Card']),
            'status' => 'Completed',
            'user_id' => User::first() ?? User::factory(),
        ];
    }
}
