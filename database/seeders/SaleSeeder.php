<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure at least one user exists
        if (User::count() == 0) {
            User::factory()->create([
                'name' => 'Cashier User',
                'email' => 'cashier@example.com',
            ]);
        }

        Sale::factory()->count(5)->create();
    }
}
