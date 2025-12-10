<?php

namespace App\Models;

use App\Enums\ManualSaleStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManualSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'manual_sale_number',
        'user_id',
        'customer_id',
        'shift_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'subtotal_before_discount',
        'tax',
        'total',
        'total_discount',
        'sale_level_discount_type',
        'sale_level_discount_value',
        'sale_level_discount_amount',
        'payment_method',
        'amount_received',
        'change_amount',
        'status',
        'reconciled_at',
        'reconciled_by',
        'converted_sale_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => ManualSaleStatusEnum::class,
            'payment_method' => PaymentMethodEnum::class,
            'subtotal' => 'decimal:2',
            'subtotal_before_discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'sale_level_discount_value' => 'decimal:2',
            'sale_level_discount_amount' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'reconciled_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ManualSaleItem::class);
    }

    // Helper methods

    public function getTotalItemDiscounts(): float
    {
        return $this->items->sum('discount_amount');
    }

    public function isPending(): bool
    {
        return $this->status === ManualSaleStatusEnum::PENDING;
    }

    public function isReconciled(): bool
    {
        return $this->status === ManualSaleStatusEnum::RECONCILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === ManualSaleStatusEnum::CANCELLED;
    }
}
