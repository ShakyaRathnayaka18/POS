<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected StockService $stockService,
        protected ShiftService $shiftService,
        protected CustomerCreditService $customerCreditService
    ) {}

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
    public function processSale(array $saleData, array $cartItems, ?string $creditTerms = null): Sale
    {
        return DB::transaction(function () use ($saleData, $cartItems, $creditTerms) {
            // Prepare items with stock allocation
            $processedItems = [];
            $subtotalBeforeDiscount = 0;
            $totalItemDiscounts = 0;

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
                    $unitPrice = $allocation['selling_price'];
                    $lineSubtotalBeforeDiscount = $allocation['quantity'] * $unitPrice;
                    $subtotalBeforeDiscount += $lineSubtotalBeforeDiscount;

                    // Apply discount if present
                    $discountData = [
                        'discount_type' => 'none',
                        'discount_value' => 0,
                        'discount_amount' => 0,
                        'discount_id' => null,
                        'discount_approved_by' => null,
                        'price_before_discount' => $unitPrice,
                        'price' => $unitPrice,
                        'subtotal_before_discount' => $lineSubtotalBeforeDiscount,
                    ];

                    if (isset($item['discount']) && $item['discount']['type'] !== 'none') {
                        $discountData = array_merge($discountData, [
                            'discount_type' => $item['discount']['type'],
                            'discount_value' => $item['discount']['value'],
                            'discount_amount' => $item['discount']['amount'],
                            'discount_id' => $item['discount']['discount_id'] ?? null,
                            'discount_approved_by' => $item['discount']['approved_by'] ?? null,
                            'price' => $item['discount']['final_price'],
                        ]);

                        $totalItemDiscounts += $item['discount']['amount'];
                    }

                    // Calculate tax on discounted price
                    $itemSubtotal = $discountData['price'] * $allocation['quantity'];
                    $itemTax = $itemSubtotal * ($allocation['tax'] / 100);

                    $processedItems[] = [
                        'product_id' => $item['product_id'],
                        'stock_id' => $allocation['stock_id'],
                        'quantity' => $allocation['quantity'],
                        'price' => $discountData['price'],
                        'price_before_discount' => $discountData['price_before_discount'],
                        'subtotal_before_discount' => $discountData['subtotal_before_discount'],
                        'discount_type' => $discountData['discount_type'],
                        'discount_value' => $discountData['discount_value'],
                        'discount_amount' => $discountData['discount_amount'],
                        'discount_id' => $discountData['discount_id'],
                        'discount_approved_by' => $discountData['discount_approved_by'],
                        'tax' => round($itemTax, 2),
                        'total' => round($itemSubtotal + $itemTax, 2),
                    ];
                }
            }

            // Apply sale-level discount if present
            $saleLevelDiscountAmount = 0;
            $saleLevelDiscountType = 'none';
            $saleLevelDiscountValue = 0;

            if (isset($saleData['sale_discount']) && $saleData['sale_discount']['type'] !== 'none') {
                $saleLevelDiscountType = $saleData['sale_discount']['type'];
                $saleLevelDiscountValue = $saleData['sale_discount']['value'];

                $subtotalAfterItemDiscounts = $subtotalBeforeDiscount - $totalItemDiscounts;

                $saleLevelDiscountAmount = match ($saleLevelDiscountType) {
                    'percentage' => ($subtotalAfterItemDiscounts * $saleLevelDiscountValue) / 100,
                    'fixed_amount' => min($saleLevelDiscountValue, $subtotalAfterItemDiscounts),
                    default => 0,
                };
            }

            // Calculate totals
            $totalDiscount = $totalItemDiscounts + $saleLevelDiscountAmount;
            $totals = [
                'subtotal_before_discount' => round($subtotalBeforeDiscount, 2),
                'total_discount' => round($totalDiscount, 2),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ];

            foreach ($processedItems as $item) {
                $totals['subtotal'] += ($item['quantity'] * $item['price']);
                $totals['tax'] += $item['tax'];
                $totals['total'] += $item['total'];
            }

            // Apply sale-level discount to subtotal
            $totals['subtotal'] = round($totals['subtotal'] - $saleLevelDiscountAmount, 2);
            $totals['tax'] = round($totals['tax'], 2);
            $totals['total'] = round($totals['subtotal'] + $totals['tax'], 2);

            // Get active shift for the user
            $activeShift = $this->shiftService->getCurrentActiveShift($saleData['user_id']);

            // Create sale record
            $sale = Sale::create([
                'sale_number' => $saleData['sale_number'],
                'user_id' => $saleData['user_id'],
                'customer_id' => $saleData['customer_id'] ?? null,
                'shift_id' => $activeShift?->id,
                'customer_name' => $saleData['customer_name'] ?? null,
                'customer_phone' => $saleData['customer_phone'] ?? null,
                'subtotal_before_discount' => $totals['subtotal_before_discount'],
                'total_discount' => $totals['total_discount'],
                'sale_level_discount_type' => $saleLevelDiscountType,
                'sale_level_discount_value' => $saleLevelDiscountValue,
                'sale_level_discount_amount' => $saleLevelDiscountAmount,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'payment_method' => $saleData['payment_method'],
                'status' => 'Completed',
            ]);

            // Create customer credit if payment method is credit
            if ($saleData['payment_method'] == 'credit' && $creditTerms && $saleData['customer_id']) {
                $this->customerCreditService->createCreditFromSale($sale, [
                    'credit_terms' => $creditTerms,
                    'invoice_date' => now(),
                ]);
            }

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

            // Create accounting journal entry after sale and items are created
            DB::afterCommit(function () use ($sale) {
                try {
                    app(\App\Services\TransactionIntegrationService::class)->createSaleJournalEntry($sale);
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to create journal entry for sale: '.$e->getMessage(), [
                        'sale_id' => $sale->id,
                        'sale_number' => $sale->sale_number,
                    ]);
                }
            });

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
