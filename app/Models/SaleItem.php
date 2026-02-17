<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'stock_id',
        'quantity',
        'price',
        'tax',
        'total',
        'discount_type',
        'discount_value',
        'discount_amount',
        'discount_id',
        'discount_approved_by',
        'price_before_discount',
        'subtotal_before_discount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'price' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'price_before_discount' => 'decimal:2',
            'subtotal_before_discount' => 'decimal:2',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function discountApprover()
    {
        return $this->belongsTo(User::class, 'discount_approved_by');
    }

    public function hasDiscount(): bool
    {
        return $this->discount_type !== 'none' && $this->discount_amount > 0;
    }

    public function getDiscountPercentage(): float
    {
        if (! $this->price_before_discount || $this->price_before_discount == 0) {
            return 0;
        }

        return ($this->discount_amount / $this->subtotal_before_discount) * 100;
    }

    /**
     * Calculate the cost for this sale item based on product type
     * CRITICAL: Handles weighted items correctly
     */
    public function calculateCost(): float
    {
        if (! $this->stock || ! $this->product) {
            return 0;
        }

        if ($this->product->is_weighted) {
            // For weighted products: quantity is in grams, cost_price is per kg
            return ($this->quantity / 1000) * $this->stock->cost_price;
        }

        // For regular products
        return $this->quantity * $this->stock->cost_price;
    }

    /**
     * Scope to get sale items for a specific product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to get sale items from sales created after a specific date
     */
    public function scopeFromSalesAfter($query, \Carbon\Carbon $date)
    {
        return $query->whereHas('sale', function ($q) use ($date) {
            $q->where('created_at', '>', $date);
        });
    }

    /**
     * Scope to get sale items for a specific date
     */
    public function scopeForDate($query, \Carbon\Carbon $date)
    {
        return $query->whereHas('sale', function ($q) use ($date) {
            $q->whereDate('created_at', $date);
        });
    }

    /**
     * Scope to eager load required relationships for profit calculation
     */
    public function scopeWithProfitData($query)
    {
        return $query->with(['product:id,is_weighted', 'stock:id,cost_price']);
    }

    /**
     * Scope to get sale items within a date range
     */
    public function scopeWithinDateRange($query, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
    {
        return $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    /**
     * Scope to get top selling products with total quantities
     */
    public function scopeTopSellingProducts($query, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate, int $limit = 10)
    {
        return $query->select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->withinDateRange($startDate, $endDate)
            ->with('product:id,product_name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit);
    }
}
