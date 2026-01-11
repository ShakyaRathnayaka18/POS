<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportWeightedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-weighted {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import weighted products from Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return 1;
        }

        $this->info('Loading Excel file...');

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $defaultCategory = Category::first();

            if (! $defaultCategory) {
                $this->error('No categories found. Please create at least one category first.');

                return 1;
            }

            $imported = 0;
            $skipped = 0;

            $this->info('Importing weighted products...');
            $progressBar = $this->output->createProgressBar(count($rows) - 1);

            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $code = $row[1] ?? null;
                $name = $row[2] ?? null;

                if (empty($code) || empty($name)) {
                    $skipped++;
                    $progressBar->advance();

                    continue;
                }

                $weightedCode = str_pad($code, 6, '0', STR_PAD_LEFT);

                if (Product::where('weighted_product_code', $weightedCode)->exists()) {
                    $skipped++;
                    $progressBar->advance();

                    continue;
                }

                Product::create([
                    'product_name' => $name,
                    'weighted_product_code' => $weightedCode,
                    'is_weighted' => true,
                    'pricing_type' => 'per_kg',
                    'allow_decimal_sales' => true,
                    'unit' => 'kg',
                    'base_unit' => 'g',
                    'conversion_factor' => 1000,
                    'category_id' => $defaultCategory->id,
                    'brand_id' => null,
                    'minimum_stock' => 0,
                    'initial_stock' => 0,
                    'description' => 'Weighted product - sold by weight',
                ]);

                $imported++;
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info("Import complete! Imported: {$imported}, Skipped: {$skipped}");

            return 0;
        } catch (\Exception $e) {
            $this->error('Error importing products: '.$e->getMessage());

            return 1;
        }
    }
}
