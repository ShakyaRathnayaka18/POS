<?php

namespace App\Enums;

enum CreditTermsEnum: string
{
    case NET_7 = 'net_7';
    case NET_15 = 'net_15';
    case NET_30 = 'net_30';
    case NET_60 = 'net_60';
    case NET_90 = 'net_90';
    case DUE_ON_RECEIPT = 'due_on_receipt';

    public function getDays(): int
    {
        return match ($this) {
            self::NET_7 => 7,
            self::NET_15 => 15,
            self::NET_30 => 30,
            self::NET_60 => 60,
            self::NET_90 => 90,
            self::DUE_ON_RECEIPT => 0,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::NET_7 => 'Net 7 Days',
            self::NET_15 => 'Net 15 Days',
            self::NET_30 => 'Net 30 Days',
            self::NET_60 => 'Net 60 Days',
            self::NET_90 => 'Net 90 Days',
            self::DUE_ON_RECEIPT => 'Due on Receipt',
        };
    }
}
