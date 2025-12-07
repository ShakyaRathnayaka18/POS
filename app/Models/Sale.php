<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'user_id',
        'customer_id',
        'shift_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax',
        'total',
        'payment_method',
        'status',
        'subtotal_before_discount',
        'total_discount',
        'sale_level_discount_type',
        'sale_level_discount_value',
        'sale_level_discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethodEnum::class,
            'subtotal_before_discount' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'sale_level_discount_value' => 'decimal:2',
            'sale_level_discount_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customerCredit()
    {
        return $this->hasOne(CustomerCredit::class);
    }

    public function getTotalItemDiscounts(): float
    {
        return $this->items->sum('discount_amount');
    }

    public function hasSaleLevelDiscount(): bool
    {
        return $this->sale_level_discount_type !== 'none' && $this->sale_level_discount_amount > 0;
    }

    public function getTotalSavings(): float
    {
        return $this->total_discount;
    }
}
