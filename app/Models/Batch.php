<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'batch_number',
        'barcode',
        'good_receive_note_id',
        'manufacture_date',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'manufacture_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function goodReceiveNote()
    {
        return $this->belongsTo(GoodReceiveNote::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            Stock::class,
            'batch_id',
            'id',
            'id',
            'product_id'
        );
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->stocks->sum('available_quantity');
    }
}
