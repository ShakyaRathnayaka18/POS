<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollSettings extends Model
{
    protected $fillable = [
        'ot_weekday_multiplier',
        'ot_weekend_multiplier',
        'daily_hours_threshold',
        'ot_calculation_mode',
        'ot_weekday_fixed_rate',
        'ot_weekend_fixed_rate',
        'epf_employee_percentage',
        'epf_employer_percentage',
        'etf_employer_percentage',
        'updated_by',
    ];

    protected $casts = [
        'ot_weekday_multiplier' => 'decimal:2',
        'ot_weekend_multiplier' => 'decimal:2',
        'daily_hours_threshold' => 'decimal:2',
        'ot_weekday_fixed_rate' => 'decimal:2',
        'ot_weekend_fixed_rate' => 'decimal:2',
        'epf_employee_percentage' => 'decimal:2',
        'epf_employer_percentage' => 'decimal:2',
        'etf_employer_percentage' => 'decimal:2',
    ];

    /**
     * Get the current payroll settings (singleton pattern).
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'ot_weekday_multiplier' => 1.5,
                'ot_weekend_multiplier' => 2.0,
                'daily_hours_threshold' => 8.0,
                'ot_calculation_mode' => 'multiplier',
                'ot_weekday_fixed_rate' => null,
                'ot_weekend_fixed_rate' => null,
                'epf_employee_percentage' => 8.0,
                'epf_employer_percentage' => 12.0,
                'etf_employer_percentage' => 3.0,
            ]
        );
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
