<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedCartItem extends Model
{
    protected $fillable = [
        'saved_cart_id',
        'product_id',
        'stock_id',
        'quantity',
        'price',
        'tax',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
    ];

    public function savedCart(): BelongsTo
    {
        return $this->belongsTo(SavedCart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
