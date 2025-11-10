<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavedCart extends Model
{
    protected $fillable = [
        'user_id',
        'cart_name',
        'customer_name',
        'customer_phone',
        'payment_method',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SavedCartItem::class);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }
}
