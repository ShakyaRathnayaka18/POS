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
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethodEnum::class,
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
}
