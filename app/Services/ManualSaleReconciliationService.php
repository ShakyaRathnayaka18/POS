<?php

namespace App\Services;

use App\Enums\ManualSaleStatusEnum;
use App\Models\Batch;
use App\Models\ManualSale;
use App\Models\Product;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ManualSaleReconciliationService
{
    public function __construct(
        protected SaleService $saleService,
        protected StockService $stockService
    ) {}

    /**
     * Find a product by barcode (searches in batches table)
     */
    public function findProductByBarcode(string $barcode): ?Product
    {
        $batch = Batch::where('barcode', $barcode)->first();

        if (! $batch) {
            return null;
        }

        return Product::with(['category', 'brand'])->find($batch->product_id);
    }

    /**
     * Validate if sufficient stock is available for a product
     */
    public function validateStockAvailability(int $productId, float $quantity): bool
    {
        try {
            $availability = $this->saleService->getProductAvailability($productId);

            return $availability['in_stock'] && $availability['available_quantity'] >= $quantity;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all pending manual sales
     */
    public function getPendingManualSales(): Collection
    {
        return ManualSale::with(['user', 'items'])
            ->where('status', ManualSaleStatusEnum::PENDING)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get reconciliation progress for a manual sale
     */
    public function getReconciliationProgress(ManualSale $sale): array
    {
        $totalItems = $sale->items->count();
        $reconciledItems = $sale->items->where('is_reconciled', true)->count();
        $unmatched = $totalItems - $reconciledItems;

        return [
            'total_items' => $totalItems,
            'reconciled_items' => $reconciledItems,
            'unmatched_items' => $unmatched,
            'is_fully_matched' => $unmatched === 0 && $totalItems > 0,
            'progress_percentage' => $totalItems > 0 ? round(($reconciledItems / $totalItems) * 100, 2) : 0,
        ];
    }

    /**
     * Reconcile a manual sale by matching items to real products and creating a regular sale
     *
     * @param  ManualSale  $manualSale  The manual sale to reconcile
     * @param  array  $matchedProducts  Array of matched products: [item_id => ['product_id' => X, 'stock_id' => Y]]
     * @return Sale The created regular sale
     *
     * @throws Exception If validation fails or stock is insufficient
     */
    public function reconcileManualSale(ManualSale $manualSale, array $matchedProducts): Sale
    {
        return DB::transaction(function () use ($manualSale, $matchedProducts) {
            // Validate that all items are matched
            $allItemIds = $manualSale->items->pluck('id')->toArray();
            $matchedItemIds = array_keys($matchedProducts);

            if (count($allItemIds) !== count($matchedItemIds)) {
                throw new Exception('Not all items have been matched to products');
            }

            // Prepare cart items for SaleService
            $cartItems = [];

            foreach ($manualSale->items as $item) {
                if (! isset($matchedProducts[$item->id])) {
                    throw new Exception("Item {$item->id} has not been matched");
                }

                $match = $matchedProducts[$item->id];

                // Validate stock availability
                if (! $this->validateStockAvailability($match['product_id'], $item->quantity)) {
                    throw new Exception("Insufficient stock for product ID {$match['product_id']}");
                }

                // Prepare cart item in the format expected by SaleService
                $cartItem = [
                    'product_id' => $match['product_id'],
                    'quantity' => $item->quantity,
                ];

                // Include discount if present
                if ($item->hasDiscount()) {
                    $cartItem['discount'] = [
                        'type' => $item->discount_type,
                        'value' => $item->discount_value,
                        'amount' => $item->discount_amount,
                        'final_price' => $item->price,
                    ];
                }

                $cartItems[] = $cartItem;
            }

            // Prepare sale data
            $saleData = [
                'sale_number' => $this->saleService->generateSaleNumber(),
                'user_id' => auth()->id() ?? $manualSale->user_id,
                'customer_id' => $manualSale->customer_id,
                'customer_name' => $manualSale->customer_name,
                'customer_phone' => $manualSale->customer_phone,
                'payment_method' => $manualSale->payment_method->value,
                'amount_received' => $manualSale->amount_received,
                'change_amount' => $manualSale->change_amount,
            ];

            // Create regular sale using SaleService (this handles stock deduction, journal entries, etc.)
            $sale = $this->saleService->processSale($saleData, $cartItems);

            // Update manual sale status
            $manualSale->update([
                'status' => ManualSaleStatusEnum::RECONCILED,
                'reconciled_at' => now(),
                'reconciled_by' => auth()->id(),
                'converted_sale_id' => $sale->id,
            ]);

            // Update manual sale items with reconciliation info
            foreach ($manualSale->items as $item) {
                if (isset($matchedProducts[$item->id])) {
                    $item->update([
                        'is_reconciled' => true,
                        'reconciled_product_id' => $matchedProducts[$item->id]['product_id'],
                        'reconciled_stock_id' => $matchedProducts[$item->id]['stock_id'] ?? null,
                    ]);
                }
            }

            return $sale->load(['items', 'user', 'customer']);
        });
    }
}
