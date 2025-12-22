<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'batch_id',
        'cost_price',
        'selling_price',
        'discount_price',
        'tax',
        'quantity',
        'available_quantity',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'tax' => 'decimal:2',
            'quantity' => 'decimal:4',
            'available_quantity' => 'decimal:4',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Check if this stock is FOC (Free of Charge).
     */
    public function isFoc(): bool
    {
        return $this->cost_price == 0;
    }

    /**
     * Scope a query to only include FOC stocks.
     */
    public function scopeFoc($query)
    {
        return $query->where('cost_price', 0);
    }

    /**
     * Scope a query to only include non-FOC stocks.
     */
    public function scopeNonFoc($query)
    {
        return $query->where('cost_price', '>', 0);
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }
}
