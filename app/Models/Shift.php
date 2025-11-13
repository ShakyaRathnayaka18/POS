<?php

namespace App\Models;

use App\Enums\ShiftStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'shift_number',
        'clock_in_at',
        'clock_out_at',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'total_sales',
        'total_sales_count',
        'notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'cash_difference' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'status' => ShiftStatusEnum::class,
        ];
    }

    /**
     * Get the user that owns the shift.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sales for the shift.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope a query to only include active shifts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ShiftStatusEnum::ACTIVE);
    }

    /**
     * Scope a query to only include completed shifts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', ShiftStatusEnum::COMPLETED);
    }

    /**
     * Scope a query to filter shifts for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate total hours worked in the shift.
     */
    public function calculateTotalHours(): float
    {
        if (! $this->clock_out_at) {
            return round($this->clock_in_at->diffInMinutes(now()) / 60, 2);
        }

        return round($this->clock_in_at->diffInMinutes($this->clock_out_at) / 60, 2);
    }

    /**
     * Calculate cash difference (actual vs expected).
     */
    public function calculateCashDifference(): ?float
    {
        if ($this->closing_cash === null || $this->expected_cash === null) {
            return null;
        }

        return round($this->closing_cash - $this->expected_cash, 2);
    }

    /**
     * Check if shift is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === ShiftStatusEnum::ACTIVE;
    }

    /**
     * Get formatted shift duration.
     */
    public function getFormattedDuration(): string
    {
        $hours = $this->calculateTotalHours();
        $wholeHours = floor($hours);
        $minutes = round(($hours - $wholeHours) * 60);

        return sprintf('%02d:%02d', $wholeHours, $minutes);
    }
}
