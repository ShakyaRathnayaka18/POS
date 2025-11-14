<?php

namespace App\Models;

use App\Enums\ExpenseStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_number',
        'expense_category_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'receipt_path',
        'status',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'status' => ExpenseStatusEnum::class,
            'payment_method' => PaymentMethodEnum::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
