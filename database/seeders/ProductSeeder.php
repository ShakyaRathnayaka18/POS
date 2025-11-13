<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $beverages = Category::where('cat_name', 'Beverages')->first();
        $foodGrains = Category::where('cat_name', 'Food Grains')->first();
        $grocery = Category::where('cat_name', 'Grocery')->first();
        $snacks = Category::where('cat_name', 'Snacks')->first();
        $stationery = Category::where('cat_name', 'Stationery')->first();

        // Get brands
        $lipton = Brand::where('brand_name', 'Lipton')->first();
        $nescafe = Brand::where('brand_name', 'Nescafe')->first();
        $cocaCola = Brand::where('brand_name', 'Coca-Cola')->first();
        $basmatiKing = Brand::where('brand_name', 'Basmati King')->first();
        $aashirvaad = Brand::where('brand_name', 'Aashirvaad')->first();
        $tata = Brand::where('brand_name', 'Tata')->first();
        $fortune = Brand::where('brand_name', 'Fortune')->first();
        $mdh = Brand::where('brand_name', 'MDH')->first();
        $britannia = Brand::where('brand_name', 'Britannia')->first();
        $lays = Brand::where('brand_name', 'Lays')->first();
        $cadbury = Brand::where('brand_name', 'Cadbury')->first();
        $classmate = Brand::where('brand_name', 'Classmate')->first();
        $reynolds = Brand::where('brand_name', 'Reynolds')->first();
        $jkCopier = Brand::where('brand_name', 'JK Copier')->first();
        $loose = Brand::where('brand_name', 'Loose/Unbranded')->first();

        $this->createProducts($beverages, $foodGrains, $grocery, $snacks, $stationery, $lipton, $nescafe, $cocaCola, $basmatiKing, $aashirvaad, $tata, $fortune, $mdh, $britannia, $lays, $cadbury, $classmate, $reynolds, $jkCopier, $loose);
    }

    private function createProducts($beverages, $foodGrains, $grocery, $snacks, $stationery, $lipton, $nescafe, $cocaCola, $basmatiKing, $aashirvaad, $tata, $fortune, $mdh, $britannia, $lays, $cadbury, $classmate, $reynolds, $jkCopier, $loose): void
    {
        $products = [
            // Beverages (6)
            ['product_name' => 'Lipton Yellow Label Tea', 'sku' => 'LIP-YLT-250G', 'item_code' => 'ITEM-00001', 'description' => 'Premium black tea 250g pack', 'initial_stock' => 0, 'minimum_stock' => 20, 'maximum_stock' => 200, 'category_id' => $beverages->id, 'brand_id' => $lipton->id, 'unit' => 'pack'],
            ['product_name' => 'Lipton Green Tea', 'sku' => 'LIP-GRN-100G', 'item_code' => 'ITEM-00002', 'description' => 'Green tea 100g pack', 'initial_stock' => 0, 'minimum_stock' => 15, 'maximum_stock' => 150, 'category_id' => $beverages->id, 'brand_id' => $lipton->id, 'unit' => 'pack'],
            ['product_name' => 'Nescafe Classic Coffee', 'sku' => 'NES-CLS-100G', 'item_code' => 'ITEM-00003', 'description' => 'Instant coffee 100g jar', 'initial_stock' => 0, 'minimum_stock' => 25, 'maximum_stock' => 180, 'category_id' => $beverages->id, 'brand_id' => $nescafe->id, 'unit' => 'jar'],
            ['product_name' => 'Coca-Cola', 'sku' => 'COKE-1L', 'item_code' => 'ITEM-00004', 'description' => 'Coca-Cola 1 liter bottle', 'initial_stock' => 0, 'minimum_stock' => 50, 'maximum_stock' => 300, 'category_id' => $beverages->id, 'brand_id' => $cocaCola->id, 'unit' => 'bottle'],
            ['product_name' => 'Coca-Cola', 'sku' => 'COKE-500ML', 'item_code' => 'ITEM-00005', 'description' => 'Coca-Cola 500ml bottle', 'initial_stock' => 0, 'minimum_stock' => 80, 'maximum_stock' => 500, 'category_id' => $beverages->id, 'brand_id' => $cocaCola->id, 'unit' => 'bottle'],
            ['product_name' => 'Loose Black Tea', 'sku' => 'LOOSE-TEA-BLK', 'item_code' => 'ITEM-00006', 'description' => 'Loose black tea sold by weight', 'initial_stock' => 0, 'minimum_stock' => 10, 'maximum_stock' => 100, 'category_id' => $beverages->id, 'brand_id' => $loose->id, 'unit' => 'kg'],
            // Food Grains (6)
            ['product_name' => 'Basmati King Premium Rice', 'sku' => 'BASK-PREM-5KG', 'item_code' => 'ITEM-00007', 'description' => 'Premium basmati rice 5kg pack', 'initial_stock' => 0, 'minimum_stock' => 30, 'maximum_stock' => 200, 'category_id' => $foodGrains->id, 'brand_id' => $basmatiKing->id, 'unit' => 'pack'],
            ['product_name' => 'Basmati King Regular Rice', 'sku' => 'BASK-REG-1KG', 'item_code' => 'ITEM-00008', 'description' => 'Regular basmati rice 1kg pack', 'initial_stock' => 0, 'minimum_stock' => 50, 'maximum_stock' => 350, 'category_id' => $foodGrains->id, 'brand_id' => $basmatiKing->id, 'unit' => 'pack'],
            ['product_name' => 'Aashirvaad Whole Wheat Flour', 'sku' => 'AASH-WW-5KG', 'item_code' => 'ITEM-00009', 'description' => 'Whole wheat flour (atta) 5kg pack', 'initial_stock' => 0, 'minimum_stock' => 40, 'maximum_stock' => 250, 'category_id' => $foodGrains->id, 'brand_id' => $aashirvaad->id, 'unit' => 'pack'],
            ['product_name' => 'Aashirvaad Whole Wheat Flour', 'sku' => 'AASH-WW-1KG', 'item_code' => 'ITEM-00010', 'description' => 'Whole wheat flour (atta) 1kg pack', 'initial_stock' => 0, 'minimum_stock' => 60, 'maximum_stock' => 400, 'category_id' => $foodGrains->id, 'brand_id' => $aashirvaad->id, 'unit' => 'pack'],
            ['product_name' => 'Loose Basmati Rice', 'sku' => 'LOOSE-RICE-BAS', 'item_code' => 'ITEM-00011', 'description' => 'Loose basmati rice sold by weight', 'initial_stock' => 0, 'minimum_stock' => 20, 'maximum_stock' => 150, 'category_id' => $foodGrains->id, 'brand_id' => $loose->id, 'unit' => 'kg'],
            ['product_name' => 'Loose Wheat Flour', 'sku' => 'LOOSE-FLOUR-WW', 'item_code' => 'ITEM-00012', 'description' => 'Loose whole wheat flour sold by weight', 'initial_stock' => 0, 'minimum_stock' => 25, 'maximum_stock' => 180, 'category_id' => $foodGrains->id, 'brand_id' => $loose->id, 'unit' => 'kg'],
            // Grocery (8)
            ['product_name' => 'Tata Salt', 'sku' => 'TATA-SALT-1KG', 'item_code' => 'ITEM-00013', 'description' => 'Iodized salt 1kg pack', 'initial_stock' => 0, 'minimum_stock' => 100, 'maximum_stock' => 600, 'category_id' => $grocery->id, 'brand_id' => $tata->id, 'unit' => 'pack'],
            ['product_name' => 'Tata Sugar', 'sku' => 'TATA-SUGAR-1KG', 'item_code' => 'ITEM-00014', 'description' => 'Refined white sugar 1kg pack', 'initial_stock' => 0, 'minimum_stock' => 80, 'maximum_stock' => 500, 'category_id' => $grocery->id, 'brand_id' => $tata->id, 'unit' => 'pack'],
            ['product_name' => 'Loose Sugar', 'sku' => 'LOOSE-SUGAR', 'item_code' => 'ITEM-00015', 'description' => 'Loose white sugar sold by weight', 'initial_stock' => 0, 'minimum_stock' => 30, 'maximum_stock' => 200, 'category_id' => $grocery->id, 'brand_id' => $loose->id, 'unit' => 'kg'],
            ['product_name' => 'Fortune Sunflower Oil', 'sku' => 'FORT-OIL-1L', 'item_code' => 'ITEM-00016', 'description' => 'Refined sunflower oil 1 liter', 'initial_stock' => 0, 'minimum_stock' => 50, 'maximum_stock' => 300, 'category_id' => $grocery->id, 'brand_id' => $fortune->id, 'unit' => 'bottle'],
            ['product_name' => 'Fortune Sunflower Oil', 'sku' => 'FORT-OIL-5L', 'item_code' => 'ITEM-00017', 'description' => 'Refined sunflower oil 5 liter', 'initial_stock' => 0, 'minimum_stock' => 20, 'maximum_stock' => 120, 'category_id' => $grocery->id, 'brand_id' => $fortune->id, 'unit' => 'can'],
            ['product_name' => 'MDH Turmeric Powder', 'sku' => 'MDH-TURM-100G', 'item_code' => 'ITEM-00018', 'description' => 'Pure turmeric powder 100g', 'initial_stock' => 0, 'minimum_stock' => 40, 'maximum_stock' => 250, 'category_id' => $grocery->id, 'brand_id' => $mdh->id, 'unit' => 'pack'],
            ['product_name' => 'MDH Red Chilli Powder', 'sku' => 'MDH-CHIL-100G', 'item_code' => 'ITEM-00019', 'description' => 'Red chilli powder 100g', 'initial_stock' => 0, 'minimum_stock' => 35, 'maximum_stock' => 220, 'category_id' => $grocery->id, 'brand_id' => $mdh->id, 'unit' => 'pack'],
            ['product_name' => 'MDH Garam Masala', 'sku' => 'MDH-GM-50G', 'item_code' => 'ITEM-00020', 'description' => 'Garam masala spice mix 50g', 'initial_stock' => 0, 'minimum_stock' => 30, 'maximum_stock' => 200, 'category_id' => $grocery->id, 'brand_id' => $mdh->id, 'unit' => 'pack'],
            // Snacks (6)
            ['product_name' => 'Britannia Good Day Biscuits', 'sku' => 'BRIT-GD-100G', 'item_code' => 'ITEM-00021', 'description' => 'Butter cookies 100g pack', 'initial_stock' => 0, 'minimum_stock' => 60, 'maximum_stock' => 400, 'category_id' => $snacks->id, 'brand_id' => $britannia->id, 'unit' => 'pack'],
            ['product_name' => 'Britannia Marie Gold', 'sku' => 'BRIT-MG-250G', 'item_code' => 'ITEM-00022', 'description' => 'Marie gold biscuits 250g pack', 'initial_stock' => 0, 'minimum_stock' => 50, 'maximum_stock' => 350, 'category_id' => $snacks->id, 'brand_id' => $britannia->id, 'unit' => 'pack'],
            ['product_name' => 'Lays Classic Salted', 'sku' => 'LAYS-CLS-52G', 'item_code' => 'ITEM-00023', 'description' => 'Classic salted potato chips 52g', 'initial_stock' => 0, 'minimum_stock' => 100, 'maximum_stock' => 600, 'category_id' => $snacks->id, 'brand_id' => $lays->id, 'unit' => 'pack'],
            ['product_name' => 'Lays American Cream & Onion', 'sku' => 'LAYS-ACO-52G', 'item_code' => 'ITEM-00024', 'description' => 'Cream and onion flavored chips 52g', 'initial_stock' => 0, 'minimum_stock' => 90, 'maximum_stock' => 550, 'category_id' => $snacks->id, 'brand_id' => $lays->id, 'unit' => 'pack'],
            ['product_name' => 'Cadbury Dairy Milk', 'sku' => 'CAD-DM-55G', 'item_code' => 'ITEM-00025', 'description' => 'Milk chocolate bar 55g', 'initial_stock' => 0, 'minimum_stock' => 120, 'maximum_stock' => 700, 'category_id' => $snacks->id, 'brand_id' => $cadbury->id, 'unit' => 'pcs'],
            ['product_name' => 'Cadbury 5 Star', 'sku' => 'CAD-5S-40G', 'item_code' => 'ITEM-00026', 'description' => '5 Star chocolate 40g', 'initial_stock' => 0, 'minimum_stock' => 100, 'maximum_stock' => 650, 'category_id' => $snacks->id, 'brand_id' => $cadbury->id, 'unit' => 'pcs'],
            // Stationery (4)
            ['product_name' => 'Classmate Notebook', 'sku' => 'CLM-NB-172P', 'item_code' => 'ITEM-00027', 'description' => 'Single line notebook 172 pages', 'initial_stock' => 0, 'minimum_stock' => 80, 'maximum_stock' => 500, 'category_id' => $stationery->id, 'brand_id' => $classmate->id, 'unit' => 'pcs'],
            ['product_name' => 'Reynolds Trimax Pen', 'sku' => 'REY-TRI-SINGLE', 'item_code' => 'ITEM-00028', 'description' => 'Blue ballpoint pen single', 'initial_stock' => 0, 'minimum_stock' => 150, 'maximum_stock' => 800, 'category_id' => $stationery->id, 'brand_id' => $reynolds->id, 'unit' => 'pcs'],
            ['product_name' => 'Reynolds Trimax Pen', 'sku' => 'REY-TRI-10PK', 'item_code' => 'ITEM-00029', 'description' => 'Blue ballpoint pen 10 pack', 'initial_stock' => 0, 'minimum_stock' => 40, 'maximum_stock' => 250, 'category_id' => $stationery->id, 'brand_id' => $reynolds->id, 'unit' => 'pack'],
            ['product_name' => 'JK Copier A4 Paper', 'sku' => 'JK-A4-500S', 'item_code' => 'ITEM-00030', 'description' => 'A4 copier paper 500 sheets (1 ream)', 'initial_stock' => 0, 'minimum_stock' => 30, 'maximum_stock' => 200, 'category_id' => $stationery->id, 'brand_id' => $jkCopier->id, 'unit' => 'ream'],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrNew(['item_code' => $productData['item_code']]);
            if (! $product->exists) {
                $product->fill($productData)->save();
            }
        }
    }
}
