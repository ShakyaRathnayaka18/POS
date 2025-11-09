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
        $this->attach('ITEM-00001', $metro, 'METRO-TEA-YL250', 32.00, 3);
        $this->attach('ITEM-00001', $beverage, 'BEV-LIPTON-YL250', 31.50, 5, true);
        $this->attach('ITEM-00002', $metro, 'METRO-TEA-GRN100', 45.00, 3);
        $this->attach('ITEM-00002', $beverage, 'BEV-LIPTON-GRN100', 44.00, 5, true);
        $this->attach('ITEM-00003', $metro, 'METRO-COFFEE-NES100', 75.00, 3);
        $this->attach('ITEM-00003', $beverage, 'BEV-NESCAFE-100', 73.50, 4, true);
        $this->attach('ITEM-00004', $metro, 'METRO-COKE-1L', 28.00, 2);
        $this->attach('ITEM-00004', $beverage, 'BEV-COLA-1000ML', 27.00, 3, true);
        $this->attach('ITEM-00004', $reliance, 'REL-COCACOLA-1L', 28.50, 4);
        $this->attach('ITEM-00005', $metro, 'METRO-COKE-500ML', 15.00, 2);
        $this->attach('ITEM-00005', $beverage, 'BEV-COLA-500ML', 14.50, 3, true);
        $this->attach('ITEM-00006', $localFood, 'LOCAL-TEA-BULK', 180.00, 2, true);
        $this->attach('ITEM-00006', $metro, 'METRO-TEA-LOOSE', 185.00, 3);

        // Food Grains - Local Food, Metro, Reliance
        $this->attach('ITEM-00007', $localFood, 'LOCAL-RICE-BASK5K', 420.00, 2, true);
        $this->attach('ITEM-00007', $metro, 'METRO-BASK-PREM5', 430.00, 3);
        $this->attach('ITEM-00008', $localFood, 'LOCAL-RICE-BASK1K', 95.00, 2, true);
        $this->attach('ITEM-00008', $reliance, 'REL-BASMATI-1KG', 97.00, 4);
        $this->attach('ITEM-00009', $localFood, 'LOCAL-ATTA-5KG', 210.00, 2, true);
        $this->attach('ITEM-00009', $metro, 'METRO-AASH-5KG', 215.00, 3);
        $this->attach('ITEM-00010', $localFood, 'LOCAL-ATTA-1KG', 48.00, 2, true);
        $this->attach('ITEM-00010', $reliance, 'REL-AASHIR-1K', 49.50, 3);
        $this->attach('ITEM-00011', $localFood, 'LOCAL-RICE-LOOSE', 65.00, 2, true);
        $this->attach('ITEM-00011', $metro, 'METRO-RICE-BULK', 68.00, 3);
        $this->attach('ITEM-00012', $localFood, 'LOCAL-FLOUR-BULK', 35.00, 2, true);
        $this->attach('ITEM-00012', $metro, 'METRO-FLOUR-LOOSE', 37.00, 3);

        // Grocery - Metro, Local Food, Reliance
        $this->attach('ITEM-00013', $metro, 'METRO-SALT-TATA1K', 18.00, 3, true);
        $this->attach('ITEM-00013', $localFood, 'LOCAL-SALT-1KG', 17.50, 2);
        $this->attach('ITEM-00014', $metro, 'METRO-SUGAR-TATA1K', 42.00, 3, true);
        $this->attach('ITEM-00014', $localFood, 'LOCAL-SUGAR-1KG', 41.00, 2);
        $this->attach('ITEM-00015', $localFood, 'LOCAL-SUGAR-BULK', 38.00, 2, true);
        $this->attach('ITEM-00015', $metro, 'METRO-SUGAR-LOOSE', 39.50, 3);
        $this->attach('ITEM-00016', $metro, 'METRO-OIL-FORT1L', 125.00, 3, true);
        $this->attach('ITEM-00016', $reliance, 'REL-FORTUNE-1L', 127.00, 4);
        $this->attach('ITEM-00017', $metro, 'METRO-OIL-FORT5L', 580.00, 3, true);
        $this->attach('ITEM-00017', $reliance, 'REL-FORTUNE-5L', 590.00, 4);
        $this->attach('ITEM-00018', $metro, 'METRO-MDH-TURM100', 45.00, 3, true);
        $this->attach('ITEM-00018', $localFood, 'LOCAL-TURMERIC-100', 43.50, 2);
        $this->attach('ITEM-00019', $metro, 'METRO-MDH-CHIL100', 48.00, 3, true);
        $this->attach('ITEM-00019', $localFood, 'LOCAL-CHILLI-100', 46.50, 2);
        $this->attach('ITEM-00020', $metro, 'METRO-MDH-GM50', 35.00, 3, true);
        $this->attach('ITEM-00020', $localFood, 'LOCAL-GARAM-50', 34.00, 2);

        // Snacks - Metro, Reliance
        $this->attach('ITEM-00021', $metro, 'METRO-BRIT-GD100', 28.00, 3, true);
        $this->attach('ITEM-00021', $reliance, 'REL-BRITANNIA-GD', 29.00, 4);
        $this->attach('ITEM-00022', $metro, 'METRO-BRIT-MG250', 32.00, 3, true);
        $this->attach('ITEM-00022', $reliance, 'REL-BRITANNIA-MG', 33.00, 4);
        $this->attach('ITEM-00023', $metro, 'METRO-LAYS-CLS52', 18.00, 2, true);
        $this->attach('ITEM-00023', $reliance, 'REL-LAYS-CLASSIC', 18.50, 3);
        $this->attach('ITEM-00024', $metro, 'METRO-LAYS-ACO52', 18.00, 2, true);
        $this->attach('ITEM-00024', $reliance, 'REL-LAYS-CREAM', 18.50, 3);
        $this->attach('ITEM-00025', $metro, 'METRO-CAD-DM55', 32.00, 2, true);
        $this->attach('ITEM-00025', $reliance, 'REL-CADBURY-DM', 33.00, 3);
        $this->attach('ITEM-00026', $metro, 'METRO-CAD-5S40', 22.00, 2, true);
        $this->attach('ITEM-00026', $reliance, 'REL-CADBURY-5STAR', 22.50, 3);

        // Stationery - Office Mart, Reliance, Metro
        $this->attach('ITEM-00027', $office, 'OFFICE-CM-NB172', 42.00, 3, true);
        $this->attach('ITEM-00027', $reliance, 'REL-CLASSMATE-NB', 44.00, 4);
        $this->attach('ITEM-00028', $office, 'OFFICE-REY-PEN1', 8.00, 2, true);
        $this->attach('ITEM-00028', $metro, 'METRO-REYNOLDS-1', 8.50, 3);
        $this->attach('ITEM-00029', $office, 'OFFICE-REY-PEN10', 75.00, 2, true);
        $this->attach('ITEM-00029', $metro, 'METRO-REYNOLDS-10', 78.00, 3);
        $this->attach('ITEM-00030', $office, 'OFFICE-JK-A4-500', 220.00, 3, true);
        $this->attach('ITEM-00030', $reliance, 'REL-JK-PAPER-A4', 225.00, 4);
        $this->attach('ITEM-00030', $metro, 'METRO-JK-A4-REAM', 228.00, 5);
    }

    private function attach(string $itemCode, Supplier $supplier, string $vendorCode, float $cost, int $leadTime, bool $preferred = false): void
    {
        $product = Product::where('item_code', $itemCode)->first();
        if ($product) {
            $product->suppliers()->syncWithoutDetaching([
                $supplier->id => [
                    'vendor_product_code' => $vendorCode,
                    'vendor_cost_price' => $cost,
                    'is_preferred' => $preferred,
                    'lead_time_days' => $leadTime,
                ]
            ]);
        }
    }
}
