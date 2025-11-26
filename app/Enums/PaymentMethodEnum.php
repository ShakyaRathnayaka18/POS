<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK = 'check';
    case CARD = 'card';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHECK => 'Check',
            self::CARD => 'Card',
            self::CREDIT => 'Credit / Pay Later',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'fas fa-money-bill-wave',
            self::BANK_TRANSFER => 'fas fa-university',
            self::CHECK => 'fas fa-money-check',
            self::CARD => 'fas fa-credit-card',
            self::CREDIT => 'fas fa-handshake',
        };
    }
}
