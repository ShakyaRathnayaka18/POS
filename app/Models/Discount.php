<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'discount_code',
        'name',
        'type',
        'value',
        'applies_to',
        'product_id',
        'category_id',
        'customer_id',
        'min_quantity',
        'min_amount',
        'start_date',
        'end_date',
        'is_active',
        'requires_approval',
        'max_discount_amount',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_quantity' => 'decimal:4',
        'min_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where(function ($q) use ($productId) {
            $q->where('applies_to', 'product')
                ->where('product_id', $productId);
        });
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->where('applies_to', 'product')
                ->where('category_id', $categoryId);
        });
    }

    public function scopeForCustomer($query, ?int $customerId)
    {
        return $query->where(function ($q) use ($customerId) {
            $q->where('applies_to', 'customer')
                ->where('customer_id', $customerId);
        });
    }

    // Helper Methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date->isAfter($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->isBefore($now)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount, float $quantity = 1): float
    {
        // Check quantity requirement
        if ($this->min_quantity && $quantity < $this->min_quantity) {
            return 0;
        }

        // Check amount requirement
        if ($this->min_amount && $amount < $this->min_amount) {
            return 0;
        }

        // Calculate discount
        $discount = match ($this->type) {
            'percentage' => ($amount * $this->value) / 100,
            'fixed_amount' => $this->value,
            default => 0,
        };

        // Apply max cap
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return round($discount, 2);
    }
}
