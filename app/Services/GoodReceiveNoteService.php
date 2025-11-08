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
        return Stock::create([
            'product_id' => $itemData['product_id'],
            'batch_id' => $batchId,
            'cost_price' => $itemData['cost_price'],
            'selling_price' => $itemData['selling_price'],
            'tax' => $itemData['tax'] ?? 0,
            'quantity' => $itemData['quantity'],
            'available_quantity' => $itemData['quantity'],
        ]);
    }

    protected function generateBatchNumber(Product $product): string
    {
        return $product->sku.'-'.now()->format('n/j/y');
    }

    public function generateGrnNumber(): string
    {
        $lastGrn = GoodReceiveNote::latest('id')->first();
        $nextNumber = $lastGrn ? ((int) substr($lastGrn->grn_number, 4)) + 1 : 1;

        return 'GRN-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
