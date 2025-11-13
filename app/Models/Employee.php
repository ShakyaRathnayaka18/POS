<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'hire_date',
        'termination_date',
        'employment_type',
        'hourly_rate',
        'base_salary',
        'pay_frequency',
        'department',
        'position',
        'epf_number',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'termination_date' => 'date',
            'hourly_rate' => 'decimal:2',
            'base_salary' => 'decimal:2',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    public function scopeSalaried($query)
    {
        return $query->where('employment_type', 'salaried');
    }

    public function scopeHourly($query)
    {
        return $query->where('employment_type', 'hourly');
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSalaried(): bool
    {
        return $this->employment_type === 'salaried';
    }

    public function isHourly(): bool
    {
        return $this->employment_type === 'hourly';
    }

    public function getPayRate(): float
    {
        return $this->isSalaried() ? (float) $this->base_salary : (float) $this->hourly_rate;
    }

    public function getFullName(): string
    {
        return $this->user ? $this->user->name : 'No User Assigned';
    }

    public static function generateEmployeeNumber(): string
    {
        $lastEmployee = self::orderBy('id', 'desc')->first();

        $sequence = $lastEmployee ? (int) substr($lastEmployee->employee_number, 3) + 1 : 1;

        return 'EMP'.str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
