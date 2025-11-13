<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'sku',
        'item_code',
        'description',
        'initial_stock',
        'minimum_stock',
        'maximum_stock',
        'product_image',
        'category_id',
        'brand_id',
        'unit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function availableStocks()
    {
        return $this->hasMany(Stock::class)->where('available_quantity', '>', 0);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot([
                'vendor_product_code',
                'vendor_cost_price',
                'is_preferred',
                'lead_time_days',
            ])
            ->withTimestamps();
    }
}
