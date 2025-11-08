<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    public function run(): void
    {
        $metro = Supplier::where('company_name', 'Metro Cash & Carry')->first();
        $reliance = Supplier::where('company_name', 'Reliance Wholesale')->first();
        $localFood = Supplier::where('company_name', 'Local Food Distributors')->first();
        $beverage = Supplier::where('company_name', 'Beverage Solutions Inc')->first();
        $office = Supplier::where('company_name', 'Office Mart Wholesale')->first();

        // Beverages - Metro, Beverage Solutions, Reliance
        $this->attach(1, $metro, 'METRO-TEA-YL250', 32.00, 3);
        $this->attach(1, $beverage, 'BEV-LIPTON-YL250', 31.50, 5, true);
        $this->attach(2, $metro, 'METRO-TEA-GRN100', 45.00, 3);
        $this->attach(2, $beverage, 'BEV-LIPTON-GRN100', 44.00, 5, true);
        $this->attach(3, $metro, 'METRO-COFFEE-NES100', 75.00, 3);
        $this->attach(3, $beverage, 'BEV-NESCAFE-100', 73.50, 4, true);
        $this->attach(4, $metro, 'METRO-COKE-1L', 28.00, 2);
        $this->attach(4, $beverage, 'BEV-COLA-1000ML', 27.00, 3, true);
        $this->attach(4, $reliance, 'REL-COCACOLA-1L', 28.50, 4);
        $this->attach(5, $metro, 'METRO-COKE-500ML', 15.00, 2);
        $this->attach(5, $beverage, 'BEV-COLA-500ML', 14.50, 3, true);
        $this->attach(6, $localFood, 'LOCAL-TEA-BULK', 180.00, 2, true);
        $this->attach(6, $metro, 'METRO-TEA-LOOSE', 185.00, 3);

        // Food Grains - Local Food, Metro, Reliance
        $this->attach(7, $localFood, 'LOCAL-RICE-BASK5K', 420.00, 2, true);
        $this->attach(7, $metro, 'METRO-BASK-PREM5', 430.00, 3);
        $this->attach(8, $localFood, 'LOCAL-RICE-BASK1K', 95.00, 2, true);
        $this->attach(8, $reliance, 'REL-BASMATI-1KG', 97.00, 4);
        $this->attach(9, $localFood, 'LOCAL-ATTA-5KG', 210.00, 2, true);
        $this->attach(9, $metro, 'METRO-AASH-5KG', 215.00, 3);
        $this->attach(10, $localFood, 'LOCAL-ATTA-1KG', 48.00, 2, true);
        $this->attach(10, $reliance, 'REL-AASHIR-1K', 49.50, 3);
        $this->attach(11, $localFood, 'LOCAL-RICE-LOOSE', 65.00, 2, true);
        $this->attach(11, $metro, 'METRO-RICE-BULK', 68.00, 3);
        $this->attach(12, $localFood, 'LOCAL-FLOUR-BULK', 35.00, 2, true);
        $this->attach(12, $metro, 'METRO-FLOUR-LOOSE', 37.00, 3);

        // Grocery - Metro, Local Food, Reliance
        $this->attach(13, $metro, 'METRO-SALT-TATA1K', 18.00, 3, true);
        $this->attach(13, $localFood, 'LOCAL-SALT-1KG', 17.50, 2);
        $this->attach(14, $metro, 'METRO-SUGAR-TATA1K', 42.00, 3, true);
        $this->attach(14, $localFood, 'LOCAL-SUGAR-1KG', 41.00, 2);
        $this->attach(15, $localFood, 'LOCAL-SUGAR-BULK', 38.00, 2, true);
        $this->attach(15, $metro, 'METRO-SUGAR-LOOSE', 39.50, 3);
        $this->attach(16, $metro, 'METRO-OIL-FORT1L', 125.00, 3, true);
        $this->attach(16, $reliance, 'REL-FORTUNE-1L', 127.00, 4);
        $this->attach(17, $metro, 'METRO-OIL-FORT5L', 580.00, 3, true);
        $this->attach(17, $reliance, 'REL-FORTUNE-5L', 590.00, 4);
        $this->attach(18, $metro, 'METRO-MDH-TURM100', 45.00, 3, true);
        $this->attach(18, $localFood, 'LOCAL-TURMERIC-100', 43.50, 2);
        $this->attach(19, $metro, 'METRO-MDH-CHIL100', 48.00, 3, true);
        $this->attach(19, $localFood, 'LOCAL-CHILLI-100', 46.50, 2);
        $this->attach(20, $metro, 'METRO-MDH-GM50', 35.00, 3, true);
        $this->attach(20, $localFood, 'LOCAL-GARAM-50', 34.00, 2);

        // Snacks - Metro, Reliance
        $this->attach(21, $metro, 'METRO-BRIT-GD100', 28.00, 3, true);
        $this->attach(21, $reliance, 'REL-BRITANNIA-GD', 29.00, 4);
        $this->attach(22, $metro, 'METRO-BRIT-MG250', 32.00, 3, true);
        $this->attach(22, $reliance, 'REL-BRITANNIA-MG', 33.00, 4);
        $this->attach(23, $metro, 'METRO-LAYS-CLS52', 18.00, 2, true);
        $this->attach(23, $reliance, 'REL-LAYS-CLASSIC', 18.50, 3);
        $this->attach(24, $metro, 'METRO-LAYS-ACO52', 18.00, 2, true);
        $this->attach(24, $reliance, 'REL-LAYS-CREAM', 18.50, 3);
        $this->attach(25, $metro, 'METRO-CAD-DM55', 32.00, 2, true);
        $this->attach(25, $reliance, 'REL-CADBURY-DM', 33.00, 3);
        $this->attach(26, $metro, 'METRO-CAD-5S40', 22.00, 2, true);
        $this->attach(26, $reliance, 'REL-CADBURY-5STAR', 22.50, 3);

        // Stationery - Office Mart, Reliance, Metro
        $this->attach(27, $office, 'OFFICE-CM-NB172', 42.00, 3, true);
        $this->attach(27, $reliance, 'REL-CLASSMATE-NB', 44.00, 4);
        $this->attach(28, $office, 'OFFICE-REY-PEN1', 8.00, 2, true);
        $this->attach(28, $metro, 'METRO-REYNOLDS-1', 8.50, 3);
        $this->attach(29, $office, 'OFFICE-REY-PEN10', 75.00, 2, true);
        $this->attach(29, $metro, 'METRO-REYNOLDS-10', 78.00, 3);
        $this->attach(30, $office, 'OFFICE-JK-A4-500', 220.00, 3, true);
        $this->attach(30, $reliance, 'REL-JK-PAPER-A4', 225.00, 4);
        $this->attach(30, $metro, 'METRO-JK-A4-REAM', 228.00, 5);
    }

    private function attach(int $productId, Supplier $supplier, string $vendorCode, float $cost, int $leadTime, bool $preferred = false): void
    {
        $product = Product::find($productId);
        if ($product) {
            $product->suppliers()->attach($supplier->id, [
                'vendor_product_code' => $vendorCode,
                'vendor_cost_price' => $cost,
                'is_preferred' => $preferred,
                'lead_time_days' => $leadTime,
            ]);
        }
    }
}
