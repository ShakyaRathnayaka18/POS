<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'sku', 'barcode', 'description',
        'initial_stock', 'minimum_stock', 'maximum_stock',
        'product_image', 'category_id', 'brand_id',
        'cost_price', 'selling_price', 'tax_rate', 'unit'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
