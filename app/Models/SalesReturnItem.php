<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_return_id',
        'sale_item_id',
        'stock_id',
        'product_id',
        'quantity_returned',
        'selling_price',
        'tax',
        'item_total',
        'condition',
        'restore_to_stock',
        'notes',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'item_total' => 'decimal:2',
        'restore_to_stock' => 'boolean',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
