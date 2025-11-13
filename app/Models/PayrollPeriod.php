<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'period_start',
        'period_end',
        'status',
        'processed_by',
        'processed_at',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'processed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'processing';
    }

    public function getTotalGrossPay(): float
    {
        return (float) $this->payrollEntries()->sum('gross_pay');
    }

    public function getTotalNetPay(): float
    {
        return (float) $this->payrollEntries()->sum('net_pay');
    }

    public function getTotalEPFEmployee(): float
    {
        return (float) $this->payrollEntries()->sum('epf_employee');
    }

    public function getTotalEPFEmployer(): float
    {
        return (float) $this->payrollEntries()->sum('epf_employer');
    }

    public function getTotalETF(): float
    {
        return (float) $this->payrollEntries()->sum('etf_employer');
    }

    public function getEmployeeCount(): int
    {
        return $this->payrollEntries()->count();
    }
}
