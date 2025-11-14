<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $fillable = [
        'payment_number',
        'supplier_id',
        'supplier_credit_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethodEnum::class,
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierCredit(): BelongsTo
    {
        return $this->belongsTo(SupplierCredit::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method->label();
    }

    public static function generatePaymentNumber(): string
    {
        $lastPayment = self::orderBy('id', 'desc')->first();
        $sequence = $lastPayment ? (int) substr($lastPayment->payment_number, 4) + 1 : 1;

        return 'PAY-'.str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
