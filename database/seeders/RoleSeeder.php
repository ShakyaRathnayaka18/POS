<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum as P;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Super Admin role (gets all permissions via Gate::before in AppServiceProvider)
        $superAdmin = Role::create(['name' => RolesEnum::SUPER_ADMIN->value]);
        $this->command->info('Created role: '.RolesEnum::SUPER_ADMIN->value);

        // Create Admin role - Full access except super admin functions
        $admin = Role::create(['name' => RolesEnum::ADMIN->value]);
        $admin->givePermissionTo([
            // Products
            P::VIEW_PRODUCTS,
            P::CREATE_PRODUCTS,
            P::EDIT_PRODUCTS,
            P::DELETE_PRODUCTS,
            // Categories
            P::VIEW_CATEGORIES,
            P::CREATE_CATEGORIES,
            P::EDIT_CATEGORIES,
            P::DELETE_CATEGORIES,
            // Brands
            P::VIEW_BRANDS,
            P::CREATE_BRANDS,
            P::EDIT_BRANDS,
            P::DELETE_BRANDS,
            // Vendor Codes
            P::VIEW_VENDOR_CODES,
            P::CREATE_VENDOR_CODES,
            P::EDIT_VENDOR_CODES,
            P::DELETE_VENDOR_CODES,
            // Suppliers
            P::VIEW_SUPPLIERS,
            P::CREATE_SUPPLIERS,
            P::EDIT_SUPPLIERS,
            P::DELETE_SUPPLIERS,
            // GRNs
            P::VIEW_GRNS,
            P::CREATE_GRNS,
            P::EDIT_GRNS,
            P::DELETE_GRNS,
            // Batches
            P::VIEW_BATCHES,
            P::VIEW_EXPIRING_BATCHES,
            // Stock
            P::VIEW_STOCKS,
            P::MANAGE_STOCK_IN,
            // Sales
            P::CREATE_SALES,
            P::VIEW_SALES,
            P::VIEW_SALE_DETAILS,
            P::MANAGE_SAVED_CARTS,
            // Sales Returns
            P::VIEW_SALES_RETURNS,
            P::CREATE_SALES_RETURNS,
            P::EDIT_SALES_RETURNS,
            P::DELETE_SALES_RETURNS,
            P::REFUND_SALES_RETURNS,
            P::CANCEL_SALES_RETURNS,
            // Supplier Returns
            P::VIEW_SUPPLIER_RETURNS,
            P::CREATE_SUPPLIER_RETURNS,
            P::EDIT_SUPPLIER_RETURNS,
            P::DELETE_SUPPLIER_RETURNS,
            P::APPROVE_SUPPLIER_RETURNS,
            P::COMPLETE_SUPPLIER_RETURNS,
            P::CANCEL_SUPPLIER_RETURNS,
            // Reports
            P::VIEW_REPORTS,
            P::VIEW_EXPENSES,
            P::MANAGE_EXPENSES,
            // Users
            P::VIEW_USERS,
            P::CREATE_USERS,
            P::EDIT_USERS,
            P::DELETE_USERS,
            P::ASSIGN_ROLES,
            // Roles
            P::VIEW_ROLES,
            P::CREATE_ROLES,
            P::EDIT_ROLES,
            P::DELETE_ROLES,
            P::VIEW_PERMISSIONS,
            P::MANAGE_PERMISSIONS,
            // Shifts
            P::MANAGE_OWN_SHIFTS,
            P::VIEW_SHIFTS,
            P::MANAGE_SHIFTS,
            P::APPROVE_SHIFTS,
            // Employees
            P::VIEW_EMPLOYEES,
            P::CREATE_EMPLOYEES,
            P::EDIT_EMPLOYEES,
            P::DELETE_EMPLOYEES,
            // Payroll
            P::VIEW_PAYROLL,
            P::PROCESS_PAYROLL,
            P::APPROVE_PAYROLL,
            P::VIEW_PAYROLL_REPORTS,
        ]);
        $this->command->info('Created role: '.RolesEnum::ADMIN->value.' with '.$admin->permissions->count().' permissions');

        // Create Manager role
        $manager = Role::create(['name' => RolesEnum::MANAGER->value]);
        $manager->givePermissionTo([
            // Products - View & Edit
            P::VIEW_PRODUCTS,
            P::EDIT_PRODUCTS,
            P::VIEW_CATEGORIES,
            P::VIEW_BRANDS,
            P::VIEW_VENDOR_CODES,
            // Suppliers
            P::VIEW_SUPPLIERS,
            P::CREATE_SUPPLIERS,
            P::EDIT_SUPPLIERS,
            // GRNs
            P::VIEW_GRNS,
            P::CREATE_GRNS,
            P::EDIT_GRNS,
            // Batches & Stock
            P::VIEW_BATCHES,
            P::VIEW_EXPIRING_BATCHES,
            P::VIEW_STOCKS,
            P::MANAGE_STOCK_IN,
            // Sales
            P::VIEW_SALES,
            P::VIEW_SALE_DETAILS,
            // Sales Returns - Full access
            P::VIEW_SALES_RETURNS,
            P::CREATE_SALES_RETURNS,
            P::REFUND_SALES_RETURNS,
            P::CANCEL_SALES_RETURNS,
            // Supplier Returns - Full access
            P::VIEW_SUPPLIER_RETURNS,
            P::CREATE_SUPPLIER_RETURNS,
            P::EDIT_SUPPLIER_RETURNS,
            P::APPROVE_SUPPLIER_RETURNS,
            P::COMPLETE_SUPPLIER_RETURNS,
            P::CANCEL_SUPPLIER_RETURNS,
            // Reports
            P::VIEW_REPORTS,
            P::VIEW_EXPENSES,
            // Shifts
            P::MANAGE_OWN_SHIFTS,
            P::VIEW_SHIFTS,
            P::APPROVE_SHIFTS,
            // Employees
            P::VIEW_EMPLOYEES,
            // Payroll
            P::VIEW_PAYROLL,
            P::APPROVE_PAYROLL,
            P::VIEW_PAYROLL_REPORTS,
        ]);
        $this->command->info('Created role: '.RolesEnum::MANAGER->value.' with '.$manager->permissions->count().' permissions');

        // Create Cashier role
        $cashier = Role::create(['name' => RolesEnum::CASHIER->value]);
        $cashier->givePermissionTo([
            // Products - View only
            P::VIEW_PRODUCTS,
            P::VIEW_CATEGORIES,
            P::VIEW_BRANDS,
            // Stock - View only
            P::VIEW_STOCKS,
            // Sales - Full POS access
            P::CREATE_SALES,
            P::VIEW_SALES,
            P::VIEW_SALE_DETAILS,
            P::MANAGE_SAVED_CARTS,
            // Sales Returns - Create & View
            P::VIEW_SALES_RETURNS,
            P::CREATE_SALES_RETURNS,
            // Shifts - Manage own shifts
            P::MANAGE_OWN_SHIFTS,
            // Payroll - View own only
            P::VIEW_OWN_PAYROLL,
        ]);
        $this->command->info('Created role: '.RolesEnum::CASHIER->value.' with '.$cashier->permissions->count().' permissions');

        // Create Stock Clerk role
        $stockClerk = Role::create(['name' => RolesEnum::STOCK_CLERK->value]);
        $stockClerk->givePermissionTo([
            // Products - View & Edit
            P::VIEW_PRODUCTS,
            P::EDIT_PRODUCTS,
            P::VIEW_CATEGORIES,
            P::VIEW_BRANDS,
            P::VIEW_VENDOR_CODES,
            // Suppliers
            P::VIEW_SUPPLIERS,
            // GRNs - Full access
            P::VIEW_GRNS,
            P::CREATE_GRNS,
            P::EDIT_GRNS,
            // Batches
            P::VIEW_BATCHES,
            P::VIEW_EXPIRING_BATCHES,
            // Stock - Full access
            P::VIEW_STOCKS,
            P::MANAGE_STOCK_IN,
            // Supplier Returns
            P::VIEW_SUPPLIER_RETURNS,
            P::CREATE_SUPPLIER_RETURNS,
        ]);
        $this->command->info('Created role: '.RolesEnum::STOCK_CLERK->value.' with '.$stockClerk->permissions->count().' permissions');

        // Create Accountant role
        $accountant = Role::create(['name' => RolesEnum::ACCOUNTANT->value]);
        $accountant->givePermissionTo([
            // View-only access
            P::VIEW_PRODUCTS,
            P::VIEW_CATEGORIES,
            P::VIEW_BRANDS,
            P::VIEW_SALES,
            P::VIEW_SALE_DETAILS,
            P::VIEW_SALES_RETURNS,
            P::VIEW_REPORTS,
            P::VIEW_EXPENSES,
            P::MANAGE_EXPENSES,
            // Employees & Payroll - Full access
            P::VIEW_EMPLOYEES,
            P::VIEW_PAYROLL,
            P::PROCESS_PAYROLL,
            P::VIEW_PAYROLL_REPORTS,
        ]);
        $this->command->info('Created role: '.RolesEnum::ACCOUNTANT->value.' with '.$accountant->permissions->count().' permissions');

        $this->command->info('All roles created successfully!');
    }
}
