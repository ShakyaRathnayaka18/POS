<?php

namespace App\Enums;

enum CreditStatusEnum: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-500',
            self::PARTIAL => 'bg-blue-500',
            self::PAID => 'bg-green-500',
            self::OVERDUE => 'bg-red-500',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Payment',
            self::PARTIAL => 'Partially Paid',
            self::PAID => 'Fully Paid',
            self::OVERDUE => 'Overdue',
        };
    }
}
