<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_number',
        'entry_date',
        'fiscal_year',
        'fiscal_period',
        'description',
        'reference_type',
        'reference_id',
        'status',
        'created_by',
        'approved_by',
        'posted_at',
        'voided_by',
        'voided_at',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'fiscal_year' => 'integer',
            'fiscal_period' => 'integer',
            'posted_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }
}
