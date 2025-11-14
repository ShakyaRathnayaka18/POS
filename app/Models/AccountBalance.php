<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountBalance extends Model
{
    protected $fillable = [
        'account_id',
        'fiscal_year',
        'fiscal_period',
        'opening_balance',
        'debit_total',
        'credit_total',
        'closing_balance',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year' => 'integer',
            'fiscal_period' => 'integer',
            'opening_balance' => 'decimal:2',
            'debit_total' => 'decimal:2',
            'credit_total' => 'decimal:2',
            'closing_balance' => 'decimal:2',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
