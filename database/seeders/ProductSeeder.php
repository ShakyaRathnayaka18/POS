<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'product_name' => 'iPhone 15 Pro',
                'sku' => 'IPH-15-PRO-256',
                'barcode' => '1234567890001',
                'description' => 'Latest iPhone with A17 Pro chip',
                'initial_stock' => 50,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'product_image' => null,
                'category_id' => 1, // Electronics
                'brand_id' => 1, // Apple
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Samsung Galaxy S24 Ultra',
                'sku' => 'SAM-S24-ULTRA-512',
                'barcode' => '1234567890002',
                'description' => 'Flagship Samsung smartphone with S Pen',
                'initial_stock' => 40,
                'minimum_stock' => 8,
                'maximum_stock' => 80,
                'product_image' => null,
                'category_id' => 1, // Electronics
                'brand_id' => 2, // Samsung
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Sony WH-1000XM5',
                'sku' => 'SNY-WH1000XM5-BLK',
                'barcode' => '1234567890003',
                'description' => 'Premium noise-cancelling wireless headphones',
                'initial_stock' => 30,
                'minimum_stock' => 5,
                'maximum_stock' => 60,
                'product_image' => null,
                'category_id' => 2, // Accessories
                'brand_id' => 3, // Sony
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Apple AirPods Pro',
                'sku' => 'APL-AIRPODS-PRO-2',
                'barcode' => '1234567890004',
                'description' => 'Wireless earbuds with active noise cancellation',
                'initial_stock' => 60,
                'minimum_stock' => 15,
                'maximum_stock' => 120,
                'product_image' => null,
                'category_id' => 2, // Accessories
                'brand_id' => 1, // Apple
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Samsung Smart TV 55"',
                'sku' => 'SAM-TV-55-QLED',
                'barcode' => '1234567890005',
                'description' => '55-inch QLED 4K Smart TV',
                'initial_stock' => 20,
                'minimum_stock' => 3,
                'maximum_stock' => 40,
                'product_image' => null,
                'category_id' => 3, // Home Appliances
                'brand_id' => 2, // Samsung
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Sony PlayStation 5',
                'sku' => 'SNY-PS5-DISC',
                'barcode' => '1234567890006',
                'description' => 'Next-gen gaming console with disc drive',
                'initial_stock' => 25,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'product_image' => null,
                'category_id' => 4, // Gaming
                'brand_id' => 3, // Sony
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Apple MacBook Pro 14"',
                'sku' => 'APL-MBP-14-M3-512',
                'barcode' => '1234567890007',
                'description' => '14-inch MacBook Pro with M3 chip',
                'initial_stock' => 15,
                'minimum_stock' => 3,
                'maximum_stock' => 30,
                'product_image' => null,
                'category_id' => 1, // Electronics
                'brand_id' => 1, // Apple
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Samsung Galaxy Buds2 Pro',
                'sku' => 'SAM-BUDS2-PRO-WHT',
                'barcode' => '1234567890008',
                'description' => 'True wireless earbuds with 360 audio',
                'initial_stock' => 45,
                'minimum_stock' => 10,
                'maximum_stock' => 90,
                'product_image' => null,
                'category_id' => 2, // Accessories
                'brand_id' => 2, // Samsung
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Sony 4K Blu-ray Player',
                'sku' => 'SNY-4K-BR-UBP-X700',
                'barcode' => '1234567890009',
                'description' => 'Ultra HD Blu-ray player with HDR',
                'initial_stock' => 18,
                'minimum_stock' => 4,
                'maximum_stock' => 35,
                'product_image' => null,
                'category_id' => 3, // Home Appliances
                'brand_id' => 3, // Sony
                'unit' => 'pcs',
            ],
            [
                'product_name' => 'Apple iPad Air',
                'sku' => 'APL-IPAD-AIR-M2-256',
                'barcode' => '1234567890010',
                'description' => 'iPad Air with M2 chip and 256GB storage',
                'initial_stock' => 35,
                'minimum_stock' => 8,
                'maximum_stock' => 70,
                'product_image' => null,
                'category_id' => 1, // Electronics
                'brand_id' => 1, // Apple
                'unit' => 'pcs',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
