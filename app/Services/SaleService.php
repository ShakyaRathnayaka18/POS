<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(protected StockService $stockService) {}

    /**
     * Generate a unique sale number
     */
    public function generateSaleNumber(): string
    {
        $lastSale = Sale::orderBy('created_at', 'desc')->first();

        if (! $lastSale) {
            return 'SALE-0001';
        }

        $lastNumber = (int) substr($lastSale->sale_number, 5);
        $newNumber = $lastNumber + 1;

        return 'SALE-'.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate totals for sale items
     */
    public function calculateTotals(array $items): array
    {
        $subtotal = 0;
        $totalTax = 0;

        foreach ($items as $item) {
            $itemSubtotal = $item['quantity'] * $item['selling_price'];
            $itemTax = $itemSubtotal * ($item['tax'] / 100);

            $subtotal += $itemSubtotal;
            $totalTax += $itemTax;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($totalTax, 2),
            'total' => round($subtotal + $totalTax, 2),
        ];
    }

    /**
     * Process a complete sale transaction
     */
    public function processSale(array $saleData, array $cartItems): Sale
    {
        return DB::transaction(function () use ($saleData, $cartItems) {
            // Prepare items with stock allocation
            $processedItems = [];

            foreach ($cartItems as $item) {
                // Allocate stock using FIFO
                $allocations = $this->stockService->allocateStock(
                    $item['product_id'],
                    $item['quantity']
                );

                if (empty($allocations)) {
                    throw new Exception("Product ID {$item['product_id']} is out of stock");
                }

                // Process each allocation (may span multiple batches)
                foreach ($allocations as $allocation) {
                    $itemSubtotal = $allocation['quantity'] * $allocation['selling_price'];
                    $itemTax = $itemSubtotal * ($allocation['tax'] / 100);

                    $processedItems[] = [
                        'product_id' => $item['product_id'],
                        'stock_id' => $allocation['stock_id'],
                        'quantity' => $allocation['quantity'],
                        'price' => $allocation['selling_price'],
                        'tax' => round($itemTax, 2),
                        'total' => round($itemSubtotal + $itemTax, 2),
                    ];
                }
            }

            // Calculate totals
            $totals = [
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ];

            foreach ($processedItems as $item) {
                $totals['subtotal'] += ($item['quantity'] * $item['price']);
                $totals['tax'] += $item['tax'];
                $totals['total'] += $item['total'];
            }

            $totals['subtotal'] = round($totals['subtotal'], 2);
            $totals['tax'] = round($totals['tax'], 2);
            $totals['total'] = round($totals['total'], 2);

            // Create sale record
            $sale = Sale::create([
                'sale_number' => $saleData['sale_number'],
                'user_id' => $saleData['user_id'],
                'customer_name' => $saleData['customer_name'] ?? null,
                'customer_phone' => $saleData['customer_phone'] ?? null,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'payment_method' => $saleData['payment_method'],
                'status' => 'Completed',
            ]);

            // Create sale items
            foreach ($processedItems as $item) {
                $item['sale_id'] = $sale->id;
                SaleItem::create($item);
            }

            // Deduct stock
            $stockAllocations = [];
            foreach ($cartItems as $item) {
                $allocations = $this->stockService->allocateStock(
                    $item['product_id'],
                    $item['quantity']
                );
                $stockAllocations = array_merge($stockAllocations, $allocations);
            }

            $this->stockService->deductStock($stockAllocations);

            return $sale->load(['items.product', 'items.stock.batch', 'user']);
        });
    }

    /**
     * Get available stock for a product with pricing info
     */
    public function getProductAvailability(int $productId): array
    {
        $availableQuantity = $this->stockService->getProductAvailableQuantity($productId);

        // Get the first available stock for pricing (FIFO)
        $stock = \App\Models\Stock::where('product_id', $productId)
            ->where('available_quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

        return [
            'available_quantity' => $availableQuantity,
            'selling_price' => $stock?->selling_price ?? 0,
            'tax' => $stock?->tax ?? 0,
            'in_stock' => $availableQuantity > 0,
        ];
    }
}
