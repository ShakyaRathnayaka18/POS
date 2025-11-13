<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'regular_hours',
        'overtime_hours',
        'overtime_hours_2x',
        'base_amount',
        'overtime_amount',
        'overtime_amount_2x',
        'gross_pay',
        'epf_employee',
        'epf_employer',
        'etf_employer',
        'other_deductions',
        'net_pay',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'regular_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'overtime_hours_2x' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'overtime_amount_2x' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'epf_employee' => 'decimal:2',
            'epf_employer' => 'decimal:2',
            'etf_employer' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
        ];
    }

    // Relationships
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class, 'payroll_entry_shift')
            ->withTimestamps();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getTotalHours(): float
    {
        return (float) $this->regular_hours + (float) $this->overtime_hours + (float) $this->overtime_hours_2x;
    }

    public function getTotalOvertimeHours(): float
    {
        return (float) $this->overtime_hours + (float) $this->overtime_hours_2x;
    }

    public function getTotalEarnings(): float
    {
        return (float) $this->base_amount + (float) $this->overtime_amount + (float) $this->overtime_amount_2x;
    }

    public function getTotalDeductions(): float
    {
        return (float) $this->epf_employee + (float) $this->other_deductions;
    }

    public function getTotalEmployerContributions(): float
    {
        return (float) $this->epf_employer + (float) $this->etf_employer;
    }

    public function calculateGrossPay(): float
    {
        return round($this->getTotalEarnings(), 2);
    }

    public function calculateNetPay(): float
    {
        return round($this->gross_pay - $this->getTotalDeductions(), 2);
    }
}
