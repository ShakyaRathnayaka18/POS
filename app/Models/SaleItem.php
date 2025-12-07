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
        if (!$this->price_before_discount || $this->price_before_discount == 0) {
            return 0;
        }

        return ($this->discount_amount / $this->subtotal_before_discount) * 100;
    }
}
