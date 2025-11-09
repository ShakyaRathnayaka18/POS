<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_return_id',
        'stock_id',
        'product_id',
        'batch_id',
        'quantity_returned',
        'cost_price',
        'tax',
        'item_total',
        'condition',
        'notes',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'item_total' => 'decimal:2',
    ];

    public function supplierReturn()
    {
        return $this->belongsTo(SupplierReturn::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
