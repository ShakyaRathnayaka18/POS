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
            [
                'brand_name' => 'Apple',
                'description' => 'Premium consumer electronics and software',
                'logo' => null,
            ],
            [
                'brand_name' => 'Samsung',
                'description' => 'South Korean multinational electronics corporation',
                'logo' => null,
            ],
            [
                'brand_name' => 'Sony',
                'description' => 'Japanese multinational conglomerate specializing in electronics',
                'logo' => null,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
