<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'sku',
        'description',
        'initial_stock',
        'minimum_stock',
        'maximum_stock',
        'product_image',
        'category_id',
        'brand_id',
        'unit',
        'base_unit',
        'purchase_unit',
        'conversion_factor',
        'allow_decimal_sales',
        'is_weighted',
        'weighted_product_code',
        'pricing_type',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:4',
            'allow_decimal_sales' => 'boolean',
            'is_weighted' => 'boolean',
        ];
    }

    /**
     * Convert purchase quantity to base unit quantity.
     * Example: 5 kg * 1000 = 5000 g
     */
    public function convertToBaseUnit(float $purchaseQuantity): float
    {
        return $purchaseQuantity * $this->conversion_factor;
    }

    /**
     * Convert base unit quantity to purchase unit quantity.
     * Example: 5000 g / 1000 = 5 kg
     */
    public function convertToPurchaseUnit(float $baseQuantity): float
    {
        if ($this->conversion_factor == 0) {
            return $baseQuantity;
        }

        return $baseQuantity / $this->conversion_factor;
    }

    /**
     * Get the display unit for sales (base_unit or unit as fallback).
     */
    public function getSalesUnitAttribute(): string
    {
        return $this->base_unit ?: $this->unit ?: 'pcs';
    }

    /**
     * Get the display unit for purchasing (purchase_unit or base_unit as fallback).
     */
    public function getPurchaseDisplayUnitAttribute(): string
    {
        return $this->purchase_unit ?: $this->base_unit ?: $this->unit ?: 'pcs';
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSku();
            }

            if ($product->is_weighted && empty($product->weighted_product_code)) {
                throw new \Exception('Weighted products must have a weighted_product_code');
            }

            if (! $product->is_weighted && ! empty($product->weighted_product_code)) {
                throw new \Exception('Regular products cannot have weighted_product_code');
            }
        });
    }

    public static function generateSku(): string
    {
        $lastProduct = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastProduct ? $lastProduct->id + 1 : 1;

        return 'SKU-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function availableStocks()
    {
        return $this->hasMany(Stock::class)->where('available_quantity', '>', 0);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot([
                'vendor_product_code',
                'is_preferred',
                'lead_time_days',
            ])
            ->withTimestamps();
    }

    /**
     * Check if product is weighted.
     */
    public function isWeightedProduct(): bool
    {
        return $this->is_weighted;
    }

    /**
     * Get the weight display unit.
     */
    public function getWeightDisplayAttribute(): string
    {
        return $this->pricing_type === 'per_kg' ? 'kg' : 'g';
    }

    /**
     * Scope for weighted products.
     */
    public function scopeWeighted($query)
    {
        return $query->where('is_weighted', true);
    }
}
