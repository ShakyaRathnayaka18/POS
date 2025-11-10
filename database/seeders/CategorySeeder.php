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
                'cat_name' => 'Beverages',
                'description' => 'Tea, Coffee, Soft Drinks, and Juices',
                'icon' => null,
            ],
            [
                'cat_name' => 'Food Grains',
                'description' => 'Rice, Wheat, Flour, and Pulses',
                'icon' => null,
            ],
            [
                'cat_name' => 'Grocery',
                'description' => 'Sugar, Salt, Oil, and Spices',
                'icon' => null,
            ],
            [
                'cat_name' => 'Snacks',
                'description' => 'Biscuits, Chips, and Chocolates',
                'icon' => null,
            ],
            [
                'cat_name' => 'Stationery',
                'description' => 'Pens, Notebooks, and Papers',
                'icon' => null,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrNew(['cat_name' => $categoryData['cat_name']]);
            if (!$category->exists) {
                $category->fill($categoryData)->save();
            }
        }
    }
}
