<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_name',
        'business_type',
        'tax_id',
        'contact_person',
        'email',
        'phone',
        'mobile',
        'payment_terms',
        'credit_limit',
        'current_credit_used',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'current_credit_used' => 'decimal:2',
        ];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot([
                'vendor_product_code',
                'vendor_cost_price',
                'is_preferred',
                'lead_time_days',
            ])
            ->withTimestamps();
    }

    public function goodReceiveNotes()
    {
        return $this->hasMany(GoodReceiveNote::class);
    }

    public function supplierCredits()
    {
        return $this->hasMany(SupplierCredit::class);
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function paymentReminders()
    {
        return $this->hasManyThrough(PaymentReminder::class, SupplierCredit::class);
    }

    public function getTotalOutstandingAttribute(): float
    {
        return $this->supplierCredits()
            ->whereNotIn('status', ['paid'])
            ->sum('outstanding_amount');
    }

    public function getCreditUtilizationAttribute(): float
    {
        if ($this->credit_limit == 0) {
            return 0;
        }

        return ($this->current_credit_used / $this->credit_limit) * 100;
    }

    public function isCreditLimitExceeded(): bool
    {
        return $this->current_credit_used > $this->credit_limit;
    }

    public function getAvailableCreditAttribute(): float
    {
        return max(0, $this->credit_limit - $this->current_credit_used);
    }
}
