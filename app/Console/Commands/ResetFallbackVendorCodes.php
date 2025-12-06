<?php

namespace App\Console\Commands;

use App\Http\Controllers\VendorCodeController;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetFallbackVendorCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:codes
                            {--dry-run : Preview changes without updating the database}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset vendor codes that fell back to using the product SKU';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Searching for vendor codes that match product SKUs (fallback values)...');
        $this->newLine();

        // Find all product_supplier records where vendor_product_code equals the product's SKU
        $fallbackRecords = DB::table('product_supplier')
            ->join('products', 'product_supplier.product_id', '=', 'products.id')
            ->join('suppliers', 'product_supplier.supplier_id', '=', 'suppliers.id')
            ->whereColumn('product_supplier.vendor_product_code', 'products.sku')
            ->select(
                'product_supplier.id',
                'product_supplier.product_id',
                'product_supplier.supplier_id',
                'product_supplier.vendor_product_code as current_code',
                'products.sku',
                'products.product_name',
                'suppliers.company_name'
            )
            ->get();

        if ($fallbackRecords->isEmpty()) {
            $this->info('No fallback vendor codes found. All vendor codes are properly set.');

            return self::SUCCESS;
        }

        $this->warn("Found {$fallbackRecords->count()} vendor code(s) using SKU as fallback:");
        $this->newLine();

        // Build table data with before/after preview
        $tableData = [];
        foreach ($fallbackRecords as $record) {
            $supplier = Supplier::find($record->supplier_id);
            $product = Product::find($record->product_id);

            $newCode = VendorCodeController::generateVendorCode($supplier, $product);

            $tableData[] = [
                'id' => $record->id,
                'product' => $record->product_name,
                'supplier' => $record->company_name,
                'current' => $record->current_code,
                'new' => $newCode,
                'supplier_model' => $supplier,
                'product_model' => $product,
            ];
        }

        // Display preview table
        $this->table(
            ['ID', 'Product', 'Supplier', 'Current Code', 'New Code'],
            collect($tableData)->map(fn ($row) => [
                $row['id'],
                $row['product'],
                $row['supplier'],
                $row['current'],
                $row['new'],
            ])->toArray()
        );

        $this->newLine();

        // Dry run mode - just show what would happen
        if ($this->option('dry-run')) {
            $this->info('Dry run mode - no changes were made.');

            return self::SUCCESS;
        }

        // Confirm before proceeding
        if (! $this->option('force')) {
            if (! $this->confirm('Do you want to update these vendor codes?')) {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        // Perform the updates
        $updated = 0;
        $this->withProgressBar($tableData, function ($row) use (&$updated) {
            DB::table('product_supplier')
                ->where('id', $row['id'])
                ->update([
                    'vendor_product_code' => $row['new'],
                    'updated_at' => now(),
                ]);
            $updated++;
        });

        $this->newLine(2);
        $this->info("Successfully updated {$updated} vendor code(s).");

        return self::SUCCESS;
    }
}
