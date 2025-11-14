<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalPeriod extends Model
{
    protected $fillable = [
        'name',
        'year',
        'month',
        'start_date',
        'end_date',
        'status',
        'closed_by',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'fiscal_period', 'month')
            ->where('fiscal_year', $this->year);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
