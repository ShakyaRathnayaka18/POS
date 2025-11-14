<?php

namespace App\Enums;

enum ReminderStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SENT => 'Sent',
            self::FAILED => 'Failed',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-500',
            self::SENT => 'bg-green-500',
            self::FAILED => 'bg-red-500',
        };
    }
}
