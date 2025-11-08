<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodReceiveNote extends Model
{
    protected $fillable = [
        'grn_number',
        'supplier_id',
        'received_date',
        'notes',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
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
}
