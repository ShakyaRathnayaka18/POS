<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPayment extends Model
{
    protected $fillable = [
        'payment_number',
        'customer_id',
        'customer_credit_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerCredit(): BelongsTo
    {
        return $this->belongsTo(CustomerCredit::class);
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
        $sequence = $lastPayment ? (int) substr($lastPayment->payment_number, 8) + 1 : 1;

        return 'CUSTPAY-'.str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
