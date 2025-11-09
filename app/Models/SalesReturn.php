<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'sale_id',
        'customer_name',
        'customer_phone',
        'return_date',
        'return_reason',
        'notes',
        'subtotal',
        'tax',
        'total',
        'refund_amount',
        'refund_method',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'processed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'Refunded');
    }
}
