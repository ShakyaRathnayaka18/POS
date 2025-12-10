<?php

namespace App\Services;

use App\Enums\ManualSaleStatusEnum;
use App\Models\ManualSale;
use App\Models\ManualSaleItem;
use Illuminate\Support\Facades\DB;

class ManualSaleService
{
    public function __construct(
        protected ShiftService $shiftService
    ) {}

    /**
     * Generate a unique manual sale number (format: MSALE-YYYYMMDD-0001)
     */
    public function generateManualSaleNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'MSALE-'.$today.'-';

        $lastSale = ManualSale::where('manual_sale_number', 'like', $prefix.'%')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $lastSale) {
            return $prefix.'0001';
        }

        $lastNumber = (int) substr($lastSale->manual_sale_number, -4);
        $newNumber = $lastNumber + 1;

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate totals for manual sale items
     */
    public function calculateTotals(array $items): array
    {
        $subtotal = 0;
        $totalTax = 0;
        $subtotalBeforeDiscount = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $itemSubtotalBeforeDiscount = $item['quantity'] * $item['price'];
            $subtotalBeforeDiscount += $itemSubtotalBeforeDiscount;

            // Apply discount if present
            $discountAmount = 0;
            if (isset($item['discount']) && $item['discount']['type'] !== 'none') {
                $discountAmount = $item['discount']['amount'];
                $totalDiscount += $discountAmount;
            }

            $itemSubtotal = $itemSubtotalBeforeDiscount - $discountAmount;
            $itemTax = $itemSubtotal * ($item['tax'] / 100);

            $subtotal += $itemSubtotal;
            $totalTax += $itemTax;
        }

        return [
            'subtotal_before_discount' => round($subtotalBeforeDiscount, 2),
            'total_discount' => round($totalDiscount, 2),
            'subtotal' => round($subtotal, 2),
            'tax' => round($totalTax, 2),
            'total' => round($subtotal + $totalTax, 2),
        ];
    }

    /**
     * Process a complete manual sale transaction
     * No stock checks or deductions - items are stored as-is for later reconciliation
     */
    public function processManualSale(array $saleData, array $manualItems): ManualSale
    {
        return DB::transaction(function () use ($saleData, $manualItems) {
            // Prepare items with discount calculations
            $processedItems = [];
            $subtotalBeforeDiscount = 0;
            $totalItemDiscounts = 0;

            foreach ($manualItems as $item) {
                $lineSubtotalBeforeDiscount = $item['quantity'] * $item['price'];
                $subtotalBeforeDiscount += $lineSubtotalBeforeDiscount;

                // Apply discount if present
                $discountData = [
                    'discount_type' => $item['discount']['type'] ?? 'none',
                    'discount_value' => $item['discount']['value'] ?? 0,
                    'discount_amount' => $item['discount']['amount'] ?? 0,
                    'price_before_discount' => $item['price'],
                    'price' => $item['price'],
                    'subtotal_before_discount' => $lineSubtotalBeforeDiscount,
                ];

                if ($discountData['discount_type'] !== 'none' && $discountData['discount_amount'] > 0) {
                    $discountData['price'] = $item['discount']['final_price'];
                    $totalItemDiscounts += $discountData['discount_amount'];
                }

                // Calculate tax on discounted price
                $itemSubtotal = $discountData['price'] * $item['quantity'];
                $itemTax = $itemSubtotal * (($item['tax'] ?? 0) / 100);

                $processedItems[] = [
                    'product_name' => $item['product_name'],
                    'entered_barcode' => $item['entered_barcode'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $discountData['price'],
                    'price_before_discount' => $discountData['price_before_discount'],
                    'subtotal' => round($itemSubtotal, 2),
                    'subtotal_before_discount' => $discountData['subtotal_before_discount'],
                    'discount_type' => $discountData['discount_type'],
                    'discount_value' => $discountData['discount_value'],
                    'discount_amount' => $discountData['discount_amount'],
                    'tax' => round($itemTax, 2),
                    'total' => round($itemSubtotal + $itemTax, 2),
                ];
            }

            // Calculate totals (no sale-level discount for now)
            $totalDiscount = $totalItemDiscounts;
            $totals = [
                'subtotal_before_discount' => round($subtotalBeforeDiscount, 2),
                'total_discount' => round($totalDiscount, 2),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ];

            foreach ($processedItems as $item) {
                $totals['subtotal'] += $item['subtotal'];
                $totals['tax'] += $item['tax'];
                $totals['total'] += $item['total'];
            }

            $totals['subtotal'] = round($totals['subtotal'], 2);
            $totals['tax'] = round($totals['tax'], 2);
            $totals['total'] = round($totals['total'], 2);

            // Get active shift for the user
            $activeShift = $this->shiftService->getCurrentActiveShift($saleData['user_id']);

            // Create manual sale record
            $manualSale = ManualSale::create([
                'manual_sale_number' => $saleData['manual_sale_number'],
                'user_id' => $saleData['user_id'],
                'customer_id' => $saleData['customer_id'] ?? null,
                'shift_id' => $activeShift?->id,
                'customer_name' => $saleData['customer_name'] ?? null,
                'customer_phone' => $saleData['customer_phone'] ?? null,
                'subtotal_before_discount' => $totals['subtotal_before_discount'],
                'total_discount' => $totals['total_discount'],
                'sale_level_discount_type' => 'none',
                'sale_level_discount_value' => 0,
                'sale_level_discount_amount' => 0,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'payment_method' => $saleData['payment_method'],
                'amount_received' => $saleData['amount_received'] ?? 0,
                'change_amount' => $saleData['change_amount'] ?? 0,
                'status' => ManualSaleStatusEnum::PENDING,
            ]);

            // Create manual sale items (no stock deduction)
            foreach ($processedItems as $itemData) {
                ManualSaleItem::create([
                    'manual_sale_id' => $manualSale->id,
                    ...$itemData,
                ]);
            }

            // Load relationships
            return $manualSale->load(['items', 'user', 'customer', 'shift']);
        });
    }
}
