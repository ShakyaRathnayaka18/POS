<?php

namespace App\Enums;

enum ShiftStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';

    /**
     * Get all shift status values as an array
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get status badge color
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-green-500 dark:bg-green-600',
            self::COMPLETED => 'bg-blue-500 dark:bg-blue-600',
            self::APPROVED => 'bg-purple-500 dark:bg-purple-600',
        };
    }

    /**
     * Get status description
     */
    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Shift is currently active',
            self::COMPLETED => 'Shift completed, pending approval',
            self::APPROVED => 'Shift approved by manager',
        };
    }
}
