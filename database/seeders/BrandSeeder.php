<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['brand_name' => 'Lipton', 'description' => 'Premium tea brand', 'logo' => null],
            ['brand_name' => 'Nescafe', 'description' => 'Instant coffee brand', 'logo' => null],
            ['brand_name' => 'Coca-Cola', 'description' => 'Global beverage company', 'logo' => null],
            ['brand_name' => 'Basmati King', 'description' => 'Premium basmati rice', 'logo' => null],
            ['brand_name' => 'Aashirvaad', 'description' => 'Wheat flour and food products', 'logo' => null],
            ['brand_name' => 'Tata', 'description' => 'Salt, sugar and grocery items', 'logo' => null],
            ['brand_name' => 'Fortune', 'description' => 'Edible oils and food products', 'logo' => null],
            ['brand_name' => 'MDH', 'description' => 'Spice manufacturer', 'logo' => null],
            ['brand_name' => 'Britannia', 'description' => 'Biscuits and bakery products', 'logo' => null],
            ['brand_name' => 'Lays', 'description' => 'Potato chips brand', 'logo' => null],
            ['brand_name' => 'Cadbury', 'description' => 'Chocolate confectionery', 'logo' => null],
            ['brand_name' => 'Classmate', 'description' => 'Student stationery products', 'logo' => null],
            ['brand_name' => 'Reynolds', 'description' => 'Writing instruments', 'logo' => null],
            ['brand_name' => 'JK Copier', 'description' => 'Paper products', 'logo' => null],
            ['brand_name' => 'Loose/Unbranded', 'description' => 'Generic unpackaged products sold by weight', 'logo' => null],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::firstOrNew(['brand_name' => $brandData['brand_name']]);
            if (!$brand->exists) {
                $brand->fill($brandData)->save();
            }
        }
    }
}
