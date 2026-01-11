<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\GoodReceiveNote;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class GoodReceiveNoteService
{
    public function createGrnWithBatches(array $grnData, array $items): GoodReceiveNote
    {
        return DB::transaction(function () use ($grnData, $items) {
            $grn = GoodReceiveNote::create($grnData);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $batchData = [
                    'barcode' => $item['barcode'] ?? null,
                    'manufacture_date' => $item['manufacture_date'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ];
                $batch = $this->createBatch($grn, $product, $batchData);
                $this->createStock($item, $batch->id);
            }

            return $grn->fresh(['batches.stocks']);
        });
    }

    public function createBatch(GoodReceiveNote $grn, Product $product, ?array $batchData = null): Batch
    {
        // For weighted products, use or create a master batch
        if ($product->is_weighted) {
            $masterBatchNumber = 'WEIGHTED-'.$product->weighted_product_code;
            $masterBarcode = 'WEIGHTED-'.$product->weighted_product_code;

            // Check if master batch already exists
            $existingBatch = Batch::where('batch_number', $masterBatchNumber)->first();

            if ($existingBatch) {
                return $existingBatch;
            }

            // Create new master batch for weighted product
            return Batch::create([
                'batch_number' => $masterBatchNumber,
                'barcode' => $masterBarcode,
                'good_receive_note_id' => $grn->id,
                'manufacture_date' => null,
                'expiry_date' => null,
                'notes' => "Master batch for weighted product: {$product->product_name}",
            ]);
        }

        // Regular product batch creation
        return Batch::create([
            'batch_number' => $batchData['batch_number'] ?? $this->generateBatchNumber($product),
            'barcode' => $batchData['barcode'] ?? null,
            'good_receive_note_id' => $grn->id,
            'manufacture_date' => $batchData['manufacture_date'] ?? now(),
            'expiry_date' => $batchData['expiry_date'] ?? null,
            'notes' => $batchData['notes'] ?? "Auto-generated batch for product: {$product->product_name} (GRN: {$grn->grn_number})",
        ]);
    }

    public function createStock(array $itemData, int $batchId): Stock
    {
        $product = Product::find($itemData['product_id']);

        // Convert purchase quantity to base unit quantity using conversion factor
        // Example: 5 kg × 1000 = 5000 g stored in stock
        $baseQuantity = $itemData['quantity'] * ($product->conversion_factor ?? 1);

        // Calculate cost and selling price per base unit
        // Example: 500 LKR per kg / 1000 = 0.50 LKR per gram
        $conversionFactor = $product->conversion_factor ?? 1;
        $costPerBaseUnit = $conversionFactor > 1
            ? $itemData['cost_price'] / $conversionFactor
            : $itemData['cost_price'];

        // Handle optional selling price
        $sellingPrice = $itemData['selling_price'] ?? null;
        $sellingPerBaseUnit = $sellingPrice
            ? ($conversionFactor > 1 ? $sellingPrice / $conversionFactor : $sellingPrice)
            : 0;

        return Stock::create([
            'product_id' => $itemData['product_id'],
            'batch_id' => $batchId,
            'cost_price' => $costPerBaseUnit,
            'selling_price' => $sellingPerBaseUnit,
            'tax' => $itemData['tax'] ?? 0,
            'quantity' => $baseQuantity,
            'available_quantity' => $baseQuantity,
        ]);
    }

    protected function generateBatchNumber(Product $product): string
    {
        $dateFormat = now()->format('n/j/y');
        $basePattern = $product->sku.'-'.$dateFormat;

        // Count existing batches with the same product SKU and date pattern
        $existingCount = Batch::where('batch_number', 'like', $basePattern.'%')->count();
        $sequence = str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT);

        return $basePattern.'-'.$sequence;
    }

    public function generateGrnNumber(): string
    {
        $lastGrn = GoodReceiveNote::latest('id')->first();
        $nextNumber = $lastGrn ? ((int) substr($lastGrn->grn_number, 4)) + 1 : 1;

        return 'GRN-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
