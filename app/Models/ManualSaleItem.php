<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'manual_sale_id',
        'product_name',
        'entered_barcode',
        'quantity',
        'price',
        'price_before_discount',
        'subtotal',
        'subtotal_before_discount',
        'tax',
        'total',
        'discount_type',
        'discount_value',
        'discount_amount',
        'is_reconciled',
        'reconciled_product_id',
        'reconciled_stock_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'price_before_discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'subtotal_before_discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'is_reconciled' => 'boolean',
        ];
    }

    // Relationships

    public function manualSale(): BelongsTo
    {
        return $this->belongsTo(ManualSale::class);
    }

    public function reconciledProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'reconciled_product_id');
    }

    public function reconciledStock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'reconciled_stock_id');
    }

    // Helper methods

    public function hasDiscount(): bool
    {
        return $this->discount_type !== 'none' && $this->discount_amount > 0;
    }

    public function isReconciled(): bool
    {
        return $this->is_reconciled;
    }

    public function getDiscountPercentage(): float
    {
        if (! $this->subtotal_before_discount || $this->subtotal_before_discount == 0) {
            return 0;
        }

        return ($this->discount_amount / $this->subtotal_before_discount) * 100;
    }
}
