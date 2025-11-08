<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'cat_name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
                'icon' => null,
            ],
            [
                'cat_name' => 'Accessories',
                'description' => 'Phone and computer accessories',
                'icon' => null,
            ],
            [
                'cat_name' => 'Home Appliances',
                'description' => 'Appliances for home use',
                'icon' => null,
            ],
            [
                'cat_name' => 'Gaming',
                'description' => 'Gaming consoles and accessories',
                'icon' => null,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
