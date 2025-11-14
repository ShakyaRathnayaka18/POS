<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit_amount',
        'credit_amount',
        'description',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'debit_amount' => 'decimal:2',
            'credit_amount' => 'decimal:2',
            'line_number' => 'integer',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
