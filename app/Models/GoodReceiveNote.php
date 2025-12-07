<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodReceiveNote extends Model
{
    protected $fillable = [
        'grn_number',
        'supplier_id',
        'received_date',
        'invoice_number',
        'invoice_date',
        'notes',
        'subtotal',
        'subtotal_before_discount',
        'tax',
        'shipping',
        'discount',
        'total',
        'status',
        'payment_type',
        'is_credit',
        'supplier_credit_id',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'subtotal_before_discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'is_credit' => 'boolean',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function supplierCredit()
    {
        return $this->hasOne(SupplierCredit::class);
    }

    public function isCredit(): bool
    {
        return $this->is_credit === true;
    }

    public function isCash(): bool
    {
        return $this->is_credit === false;
    }

    public function getPaymentStatus(): string
    {
        if ($this->isCash()) {
            return 'Paid (Cash)';
        }

        if ($this->supplierCredit) {
            return $this->supplierCredit->status->description();
        }

        return 'N/A';
    }
}
