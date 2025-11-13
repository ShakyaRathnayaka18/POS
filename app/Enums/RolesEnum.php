<?php

namespace App\Enums;

enum RolesEnum: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case CASHIER = 'Cashier';
    case STOCK_CLERK = 'Stock Clerk';
    case ACCOUNTANT = 'Accountant';

    /**
     * Get all role values as an array
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get role description
     */
    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Full system access with all permissions',
            self::ADMIN => 'Administrative access to manage users, roles, and all modules',
            self::MANAGER => 'Manage inventory, approve returns, view reports',
            self::CASHIER => 'Point of sale operations and sales management',
            self::STOCK_CLERK => 'Inventory and stock management only',
            self::ACCOUNTANT => 'View-only access to reports and expenses',
        };
    }
}
