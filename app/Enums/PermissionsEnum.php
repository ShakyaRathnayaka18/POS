<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // Product Management
    case VIEW_PRODUCTS = 'view products';
    case CREATE_PRODUCTS = 'create products';
    case EDIT_PRODUCTS = 'edit products';
    case DELETE_PRODUCTS = 'delete products';

    // Category Management
    case VIEW_CATEGORIES = 'view categories';
    case CREATE_CATEGORIES = 'create categories';
    case EDIT_CATEGORIES = 'edit categories';
    case DELETE_CATEGORIES = 'delete categories';

    // Brand Management
    case VIEW_BRANDS = 'view brands';
    case CREATE_BRANDS = 'create brands';
    case EDIT_BRANDS = 'edit brands';
    case DELETE_BRANDS = 'delete brands';

    // Vendor Code Management
    case VIEW_VENDOR_CODES = 'view vendor codes';
    case CREATE_VENDOR_CODES = 'create vendor codes';
    case EDIT_VENDOR_CODES = 'edit vendor codes';
    case DELETE_VENDOR_CODES = 'delete vendor codes';

    // Supplier Management
    case VIEW_SUPPLIERS = 'view suppliers';
    case CREATE_SUPPLIERS = 'create suppliers';
    case EDIT_SUPPLIERS = 'edit suppliers';
    case DELETE_SUPPLIERS = 'delete suppliers';

    // Inventory Management (GRN - Good Receive Notes)
    case VIEW_GRNS = 'view grns';
    case CREATE_GRNS = 'create grns';
    case EDIT_GRNS = 'edit grns';
    case DELETE_GRNS = 'delete grns';

    // Batch Management
    case VIEW_BATCHES = 'view batches';
    case VIEW_EXPIRING_BATCHES = 'view expiring batches';

    // Stock Management
    case VIEW_STOCKS = 'view stocks';
    case MANAGE_STOCK_IN = 'manage stock in';

    // Sales (POS)
    case CREATE_SALES = 'create sales';
    case VIEW_SALES = 'view sales';
    case VIEW_SALE_DETAILS = 'view sale details';

    // Sales Returns
    case VIEW_SALES_RETURNS = 'view sales returns';
    case CREATE_SALES_RETURNS = 'create sales returns';
    case EDIT_SALES_RETURNS = 'edit sales returns';
    case DELETE_SALES_RETURNS = 'delete sales returns';
    case REFUND_SALES_RETURNS = 'refund sales returns';
    case CANCEL_SALES_RETURNS = 'cancel sales returns';

    // Supplier Returns
    case VIEW_SUPPLIER_RETURNS = 'view supplier returns';
    case CREATE_SUPPLIER_RETURNS = 'create supplier returns';
    case EDIT_SUPPLIER_RETURNS = 'edit supplier returns';
    case DELETE_SUPPLIER_RETURNS = 'delete supplier returns';
    case APPROVE_SUPPLIER_RETURNS = 'approve supplier returns';
    case COMPLETE_SUPPLIER_RETURNS = 'complete supplier returns';
    case CANCEL_SUPPLIER_RETURNS = 'cancel supplier returns';

    // Reports & Analytics
    case VIEW_REPORTS = 'view reports';
    case VIEW_EXPENSES = 'view expenses';
    case MANAGE_EXPENSES = 'manage expenses';

    // User Management
    case VIEW_USERS = 'view users';
    case CREATE_USERS = 'create users';
    case EDIT_USERS = 'edit users';
    case DELETE_USERS = 'delete users';
    case ASSIGN_ROLES = 'assign roles';

    // Role & Permission Management
    case VIEW_ROLES = 'view roles';
    case CREATE_ROLES = 'create roles';
    case EDIT_ROLES = 'edit roles';
    case DELETE_ROLES = 'delete roles';
    case VIEW_PERMISSIONS = 'view permissions';
    case MANAGE_PERMISSIONS = 'manage permissions';

    // Cart Management (for cashiers)
    case MANAGE_SAVED_CARTS = 'manage saved carts';

    // Shift Management
    case MANAGE_OWN_SHIFTS = 'manage own shifts';
    case VIEW_SHIFTS = 'view shifts';
    case MANAGE_SHIFTS = 'manage shifts';
    case APPROVE_SHIFTS = 'approve shifts';

    // Employee Management
    case VIEW_EMPLOYEES = 'view employees';
    case CREATE_EMPLOYEES = 'create employees';
    case EDIT_EMPLOYEES = 'edit employees';
    case DELETE_EMPLOYEES = 'delete employees';

    // Payroll Management
    case VIEW_PAYROLL = 'view payroll';
    case PROCESS_PAYROLL = 'process payroll';
    case APPROVE_PAYROLL = 'approve payroll';
    case VIEW_OWN_PAYROLL = 'view own payroll';
    case VIEW_PAYROLL_REPORTS = 'view payroll reports';

    /**
     * Get all permission values as an array
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get permissions grouped by module
     */
    public static function grouped(): array
    {
        return [
            'Products' => [
                self::VIEW_PRODUCTS,
                self::CREATE_PRODUCTS,
                self::EDIT_PRODUCTS,
                self::DELETE_PRODUCTS,
            ],
            'Categories' => [
                self::VIEW_CATEGORIES,
                self::CREATE_CATEGORIES,
                self::EDIT_CATEGORIES,
                self::DELETE_CATEGORIES,
            ],
            'Brands' => [
                self::VIEW_BRANDS,
                self::CREATE_BRANDS,
                self::EDIT_BRANDS,
                self::DELETE_BRANDS,
            ],
            'Vendor Codes' => [
                self::VIEW_VENDOR_CODES,
                self::CREATE_VENDOR_CODES,
                self::EDIT_VENDOR_CODES,
                self::DELETE_VENDOR_CODES,
            ],
            'Suppliers' => [
                self::VIEW_SUPPLIERS,
                self::CREATE_SUPPLIERS,
                self::EDIT_SUPPLIERS,
                self::DELETE_SUPPLIERS,
            ],
            'Good Receive Notes' => [
                self::VIEW_GRNS,
                self::CREATE_GRNS,
                self::EDIT_GRNS,
                self::DELETE_GRNS,
            ],
            'Batches' => [
                self::VIEW_BATCHES,
                self::VIEW_EXPIRING_BATCHES,
            ],
            'Stock' => [
                self::VIEW_STOCKS,
                self::MANAGE_STOCK_IN,
            ],
            'Sales' => [
                self::CREATE_SALES,
                self::VIEW_SALES,
                self::VIEW_SALE_DETAILS,
                self::MANAGE_SAVED_CARTS,
            ],
            'Sales Returns' => [
                self::VIEW_SALES_RETURNS,
                self::CREATE_SALES_RETURNS,
                self::EDIT_SALES_RETURNS,
                self::DELETE_SALES_RETURNS,
                self::REFUND_SALES_RETURNS,
                self::CANCEL_SALES_RETURNS,
            ],
            'Supplier Returns' => [
                self::VIEW_SUPPLIER_RETURNS,
                self::CREATE_SUPPLIER_RETURNS,
                self::EDIT_SUPPLIER_RETURNS,
                self::DELETE_SUPPLIER_RETURNS,
                self::APPROVE_SUPPLIER_RETURNS,
                self::COMPLETE_SUPPLIER_RETURNS,
                self::CANCEL_SUPPLIER_RETURNS,
            ],
            'Reports & Expenses' => [
                self::VIEW_REPORTS,
                self::VIEW_EXPENSES,
                self::MANAGE_EXPENSES,
            ],
            'User Management' => [
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::EDIT_USERS,
                self::DELETE_USERS,
                self::ASSIGN_ROLES,
            ],
            'Roles & Permissions' => [
                self::VIEW_ROLES,
                self::CREATE_ROLES,
                self::EDIT_ROLES,
                self::DELETE_ROLES,
                self::VIEW_PERMISSIONS,
                self::MANAGE_PERMISSIONS,
            ],
            'Shifts' => [
                self::MANAGE_OWN_SHIFTS,
                self::VIEW_SHIFTS,
                self::MANAGE_SHIFTS,
                self::APPROVE_SHIFTS,
            ],
            'Employees' => [
                self::VIEW_EMPLOYEES,
                self::CREATE_EMPLOYEES,
                self::EDIT_EMPLOYEES,
                self::DELETE_EMPLOYEES,
            ],
            'Payroll' => [
                self::VIEW_PAYROLL,
                self::PROCESS_PAYROLL,
                self::APPROVE_PAYROLL,
                self::VIEW_OWN_PAYROLL,
                self::VIEW_PAYROLL_REPORTS,
            ],
        ];
    }
}
