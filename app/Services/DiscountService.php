<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * Find applicable discounts for a product
     */
    public function findApplicableDiscounts(
        int $productId,
        ?int $customerId = null,
        float $quantity = 1,
        float $amount = 0
    ): Collection {
        $product = Product::with('category')->find($productId);

        if (!$product) {
            return collect();
        }

        return Discount::active()
            ->where(function ($query) use ($product, $customerId) {
                // Product-specific discounts
                $query->where(function ($q) use ($product) {
                    $q->where('applies_to', 'product')
                        ->where(function ($q2) use ($product) {
                            $q2->where('product_id', $product->id)
                                ->orWhere('category_id', $product->category_id);
                        });
                });

                // Customer-specific discounts
                if ($customerId) {
                    $query->orWhere(function ($q) use ($customerId) {
                        $q->where('applies_to', 'customer')
                            ->where('customer_id', $customerId);
                    });
                }

                // Sale-level discounts (handled separately)
                $query->orWhere('applies_to', 'sale');
            })
            ->where(function ($query) use ($quantity, $amount) {
                $query->where(function ($q) use ($quantity) {
                    $q->whereNull('min_quantity')
                        ->orWhere('min_quantity', '<=', $quantity);
                })
                ->where(function ($q) use ($amount) {
                    $q->whereNull('min_amount')
                        ->orWhere('min_amount', '<=', $amount);
                });
            })
            ->orderBy('value', 'desc')
            ->get();
    }

    /**
     * Get the best discount for a product
     */
    public function getBestDiscount(
        int $productId,
        float $quantity,
        float $unitPrice,
        ?int $customerId = null
    ): ?Discount {
        $amount = $quantity * $unitPrice;
        $discounts = $this->findApplicableDiscounts($productId, $customerId, $quantity, $amount);

        if ($discounts->isEmpty()) {
            return null;
        }

        // Calculate actual discount amounts and return the best one
        $bestDiscount = null;
        $maxDiscountAmount = 0;

        foreach ($discounts as $discount) {
            $discountAmount = $discount->calculateDiscount($amount, $quantity);

            if ($discountAmount > $maxDiscountAmount) {
                $maxDiscountAmount = $discountAmount;
                $bestDiscount = $discount;
            }
        }

        return $bestDiscount;
    }

    /**
     * Apply discount to a line item
     */
    public function applyDiscount(
        Discount $discount,
        float $quantity,
        float $unitPrice
    ): array {
        $subtotalBeforeDiscount = $quantity * $unitPrice;
        $discountAmount = $discount->calculateDiscount($subtotalBeforeDiscount, $quantity);
        $subtotalAfterDiscount = $subtotalBeforeDiscount - $discountAmount;
        $finalUnitPrice = $quantity > 0 ? $subtotalAfterDiscount / $quantity : $unitPrice;

        return [
            'discount_type' => $discount->type,
            'discount_value' => $discount->value,
            'discount_amount' => $discountAmount,
            'discount_id' => $discount->id,
            'price_before_discount' => $unitPrice,
            'price' => $finalUnitPrice,
            'subtotal_before_discount' => $subtotalBeforeDiscount,
            'subtotal_after_discount' => $subtotalAfterDiscount,
        ];
    }

    /**
     * Apply manual discount (cashier override)
     */
    public function applyManualDiscount(
        string $type,
        float $value,
        float $quantity,
        float $unitPrice
    ): array {
        $subtotalBeforeDiscount = $quantity * $unitPrice;

        $discountAmount = match ($type) {
            'percentage' => ($subtotalBeforeDiscount * $value) / 100,
            'fixed_amount' => min($value, $subtotalBeforeDiscount), // Can't exceed subtotal
            default => 0,
        };

        $subtotalAfterDiscount = $subtotalBeforeDiscount - $discountAmount;
        $finalUnitPrice = $quantity > 0 ? $subtotalAfterDiscount / $quantity : $unitPrice;

        return [
            'discount_type' => $type,
            'discount_value' => $value,
            'discount_amount' => $discountAmount,
            'discount_id' => null, // Manual discount
            'price_before_discount' => $unitPrice,
            'price' => $finalUnitPrice,
            'subtotal_before_discount' => $subtotalBeforeDiscount,
            'subtotal_after_discount' => $subtotalAfterDiscount,
        ];
    }

    /**
     * Check if discount requires approval
     */
    public function requiresApproval(Discount $discount): bool
    {
        return $discount->requires_approval;
    }

    /**
     * Check if manual discount exceeds threshold (requires approval)
     */
    public function manualDiscountRequiresApproval(float $discountPercentage): bool
    {
        $threshold = config('pos.manual_discount_approval_threshold', 20); // 20% default
        return $discountPercentage > $threshold;
    }
}
