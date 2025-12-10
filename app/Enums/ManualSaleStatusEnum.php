<?php

namespace App\Enums;

enum ManualSaleStatusEnum: string
{
    case PENDING = 'pending';
    case RECONCILED = 'reconciled';
    case CANCELLED = 'cancelled';

    /**
     * Get all manual sale status values as an array
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
            self::PENDING => 'bg-yellow-500 dark:bg-yellow-600',
            self::RECONCILED => 'bg-green-500 dark:bg-green-600',
            self::CANCELLED => 'bg-red-500 dark:bg-red-600',
        };
    }

    /**
     * Get status description
     */
    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Manual sale pending reconciliation',
            self::RECONCILED => 'Manual sale reconciled to regular sale',
            self::CANCELLED => 'Manual sale cancelled',
        };
    }
}
