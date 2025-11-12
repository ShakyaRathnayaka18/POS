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
            // Food Categories
            [
                'cat_name' => 'Beverages',
                'description' => 'Tea, Coffee, Soft Drinks, and Juices',
                'icon' => 'beverages.png',
            ],
            [
                'cat_name' => 'Bakery',
                'description' => 'Bread, Pastries, Cakes, and Baked Goods',
                'icon' => 'bakery.png',
            ],
            [
                'cat_name' => 'Dairy',
                'description' => 'Milk, Cheese, Yogurt, and Dairy Products',
                'icon' => 'dairy.png',
            ],
            [
                'cat_name' => 'Meat',
                'description' => 'Fresh Meat, Chicken, Beef, and Pork',
                'icon' => 'meat.png',
            ],
            [
                'cat_name' => 'Seafood',
                'description' => 'Fish, Shrimp, and Other Seafood',
                'icon' => 'seafood.png',
            ],
            [
                'cat_name' => 'Fruits',
                'description' => 'Fresh Fruits and Seasonal Produce',
                'icon' => 'fruits.png',
            ],
            [
                'cat_name' => 'Vegetables',
                'description' => 'Fresh Vegetables and Greens',
                'icon' => 'vegetables.png',
            ],
            [
                'cat_name' => 'Frozen Foods',
                'description' => 'Frozen Meals, Ice Cream, and Frozen Goods',
                'icon' => 'frozen.png',
            ],
            // Grocery
            [
                'cat_name' => 'Snacks',
                'description' => 'Chips, Crackers, and Savory Snacks',
                'icon' => 'snacks.png',
            ],
            [
                'cat_name' => 'Sweets',
                'description' => 'Candy, Chocolates, and Sweet Treats',
                'icon' => 'sweets.png',
            ],
            [
                'cat_name' => 'Spices',
                'description' => 'Herbs, Spices, and Seasonings',
                'icon' => 'spices.png',
            ],
            [
                'cat_name' => 'Grains',
                'description' => 'Rice, Wheat, Flour, and Pulses',
                'icon' => 'grains.png',
            ],
            [
                'cat_name' => 'Canned Goods',
                'description' => 'Canned Foods and Preserved Items',
                'icon' => 'canned.png',
            ],
            // Household
            [
                'cat_name' => 'Cleaning Products',
                'description' => 'Detergents, Cleaners, and Sanitizers',
                'icon' => 'cleaning.png',
            ],
            [
                'cat_name' => 'Personal Care',
                'description' => 'Shampoo, Soap, and Personal Hygiene',
                'icon' => 'personal-care.png',
            ],
            [
                'cat_name' => 'Baby Products',
                'description' => 'Diapers, Baby Food, and Baby Care',
                'icon' => 'baby.png',
            ],
            [
                'cat_name' => 'Health & Medicine',
                'description' => 'Over-the-Counter Medicine and Health Products',
                'icon' => 'medicine.png',
            ],
            // Other
            [
                'cat_name' => 'Electronics',
                'description' => 'Small Electronics and Gadgets',
                'icon' => 'electronics.png',
            ],
            [
                'cat_name' => 'Pet Supplies',
                'description' => 'Pet Food, Toys, and Pet Care Products',
                'icon' => 'pet-supplies.png',
            ],
            [
                'cat_name' => 'Household Items',
                'description' => 'Kitchen Tools, Storage, and Home Essentials',
                'icon' => 'household.png',
            ],
            [
                'cat_name' => 'Stationery',
                'description' => 'Pens, Notebooks, Papers, and Office Supplies',
                'icon' => 'stationery.png',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrNew(['cat_name' => $categoryData['cat_name']]);
            if (! $category->exists) {
                $category->fill($categoryData)->save();
            }
        }
    }
}
