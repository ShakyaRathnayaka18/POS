<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_number',
        'stock_id',
        'product_id',
        'batch_id',
        'type',
        'quantity_before',
        'quantity_adjusted',
        'quantity_after',
        'cost_price',
        'total_value',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'adjustment_date',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity_before' => 'decimal:4',
            'quantity_adjusted' => 'decimal:4',
            'quantity_after' => 'decimal:4',
            'cost_price' => 'decimal:2',
            'total_value' => 'decimal:2',
            'adjustment_date' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }

    // Helper methods
    public function isIncrease(): bool
    {
        return $this->type === 'increase';
    }

    public function isDecrease(): bool
    {
        return $this->type === 'decrease';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
