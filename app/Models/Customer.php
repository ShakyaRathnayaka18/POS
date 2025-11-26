<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_number',
        'name',
        'email',
        'phone',
        'mobile',
        'address',
        'tax_id',
        'credit_limit',
        'current_credit_used',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'current_credit_used' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function customerCredits()
    {
        return $this->hasMany(CustomerCredit::class);
    }

    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function getTotalOutstandingAttribute(): float
    {
        return $this->customerCredits()
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

    public static function generateCustomerNumber(): string
    {
        $latestCustomer = self::latest('id')->first();
        $nextNumber = $latestCustomer ? $latestCustomer->id + 1 : 1;

        return 'CUST-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithCreditActivity($query)
    {
        return $query->with(['customerCredits', 'customerPayments']);
    }
}
