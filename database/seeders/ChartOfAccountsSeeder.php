<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $assetType = AccountType::where('name', 'Asset')->first();
        $liabilityType = AccountType::where('name', 'Liability')->first();
        $equityType = AccountType::where('name', 'Equity')->first();
        $revenueType = AccountType::where('name', 'Revenue')->first();
        $expenseType = AccountType::where('name', 'Expense')->first();

        $accounts = [
            // ASSETS (1000-1999)
            [
                'account_code' => '1000',
                'account_name' => 'Current Assets',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Assets expected to be converted to cash within one year',
                'is_active' => true,
            ],
            [
                'account_code' => '1100',
                'account_name' => 'Cash and Cash Equivalents',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Cash on hand and in bank accounts',
                'is_active' => true,
            ],
            [
                'account_code' => '1110',
                'account_name' => 'Cash in Hand',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Physical cash at the business location',
                'is_active' => true,
            ],
            [
                'account_code' => '1120',
                'account_name' => 'Cash in Bank',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Cash held in bank accounts',
                'is_active' => true,
            ],
            [
                'account_code' => '1200',
                'account_name' => 'Accounts Receivable',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Money owed to the business by customers',
                'is_active' => true,
            ],
            [
                'account_code' => '1300',
                'account_name' => 'Inventory',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Goods held for sale',
                'is_active' => true,
            ],
            [
                'account_code' => '1310',
                'account_name' => 'Inventory Adjustment Gain',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Inventory increases from recounts or found items',
                'is_active' => true,
            ],
            [
                'account_code' => '1500',
                'account_name' => 'Fixed Assets',
                'account_type_id' => $assetType->id,
                'parent_account_id' => null,
                'description' => 'Long-term tangible assets',
                'is_active' => true,
            ],

            // LIABILITIES (2000-2999)
            [
                'account_code' => '2000',
                'account_name' => 'Current Liabilities',
                'account_type_id' => $liabilityType->id,
                'parent_account_id' => null,
                'description' => 'Obligations due within one year',
                'is_active' => true,
            ],
            [
                'account_code' => '2100',
                'account_name' => 'Accounts Payable',
                'account_type_id' => $liabilityType->id,
                'parent_account_id' => null,
                'description' => 'Money owed to suppliers',
                'is_active' => true,
            ],
            [
                'account_code' => '2200',
                'account_name' => 'Salaries Payable',
                'account_type_id' => $liabilityType->id,
                'parent_account_id' => null,
                'description' => 'Unpaid employee salaries',
                'is_active' => true,
            ],
            [
                'account_code' => '2210',
                'account_name' => 'EPF Payable',
                'account_type_id' => $liabilityType->id,
                'parent_account_id' => null,
                'description' => 'Employee Provident Fund contributions payable',
                'is_active' => true,
            ],
            [
                'account_code' => '2220',
                'account_name' => 'ETF Payable',
                'account_type_id' => $liabilityType->id,
                'parent_account_id' => null,
                'description' => 'Employees Trust Fund contributions payable',
                'is_active' => true,
            ],

            // EQUITY (3000-3999)
            [
                'account_code' => '3000',
                'account_name' => 'Owner\'s Equity',
                'account_type_id' => $equityType->id,
                'parent_account_id' => null,
                'description' => 'Owner\'s investment and retained earnings',
                'is_active' => true,
            ],
            [
                'account_code' => '3100',
                'account_name' => 'Capital',
                'account_type_id' => $equityType->id,
                'parent_account_id' => null,
                'description' => 'Initial and additional investments by owner',
                'is_active' => true,
            ],
            [
                'account_code' => '3200',
                'account_name' => 'Retained Earnings',
                'account_type_id' => $equityType->id,
                'parent_account_id' => null,
                'description' => 'Cumulative net income retained in the business',
                'is_active' => true,
            ],

            // REVENUE (4000-4999)
            [
                'account_code' => '4000',
                'account_name' => 'Sales Revenue',
                'account_type_id' => $revenueType->id,
                'parent_account_id' => null,
                'description' => 'Revenue from sales of goods',
                'is_active' => true,
            ],
            [
                'account_code' => '4100',
                'account_name' => 'Product Sales',
                'account_type_id' => $revenueType->id,
                'parent_account_id' => null,
                'description' => 'Revenue from product sales',
                'is_active' => true,
            ],
            [
                'account_code' => '4200',
                'account_name' => 'Sales Returns and Allowances',
                'account_type_id' => $revenueType->id,
                'parent_account_id' => null,
                'description' => 'Contra-revenue account for returns',
                'is_active' => true,
            ],

            // COST OF GOODS SOLD (5000-5999)
            [
                'account_code' => '5000',
                'account_name' => 'Cost of Goods Sold',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Direct costs of goods sold',
                'is_active' => true,
            ],
            [
                'account_code' => '5100',
                'account_name' => 'Purchases',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Cost of inventory purchased',
                'is_active' => true,
            ],

            // OPERATING EXPENSES (6000-9999)
            [
                'account_code' => '6000',
                'account_name' => 'Operating Expenses',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Regular business operating expenses',
                'is_active' => true,
            ],
            [
                'account_code' => '6100',
                'account_name' => 'Salaries and Wages',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Employee compensation expenses',
                'is_active' => true,
            ],
            [
                'account_code' => '6200',
                'account_name' => 'Rent Expense',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Cost of renting business premises',
                'is_active' => true,
            ],
            [
                'account_code' => '6300',
                'account_name' => 'Utilities Expense',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Electricity, water, internet, etc.',
                'is_active' => true,
            ],
            [
                'account_code' => '6400',
                'account_name' => 'Office Supplies Expense',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Cost of office supplies consumed',
                'is_active' => true,
            ],
            [
                'account_code' => '6500',
                'account_name' => 'Marketing and Advertising',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Marketing and promotional expenses',
                'is_active' => true,
            ],
            [
                'account_code' => '6600',
                'account_name' => 'Maintenance and Repairs',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Costs to maintain and repair assets',
                'is_active' => true,
            ],
            [
                'account_code' => '6700',
                'account_name' => 'Transportation Expense',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Delivery and transportation costs',
                'is_active' => true,
            ],
            [
                'account_code' => '6800',
                'account_name' => 'Professional Fees',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Legal, accounting, and consulting fees',
                'is_active' => true,
            ],
            [
                'account_code' => '6900',
                'account_name' => 'Miscellaneous Expenses',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Other operating expenses',
                'is_active' => true,
            ],
            [
                'account_code' => '7100',
                'account_name' => 'Inventory Loss/Shrinkage',
                'account_type_id' => $expenseType->id,
                'parent_account_id' => null,
                'description' => 'Loss of inventory due to damage, theft, or obsolescence',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }

        // Set parent accounts after all accounts are created
        $this->setParentAccounts();
    }

    private function setParentAccounts(): void
    {
        $parentMappings = [
            '1110' => '1100', // Cash in Hand -> Cash and Cash Equivalents
            '1120' => '1100', // Cash in Bank -> Cash and Cash Equivalents
            '1100' => '1000', // Cash and Cash Equivalents -> Current Assets
            '1200' => '1000', // Accounts Receivable -> Current Assets
            '1300' => '1000', // Inventory -> Current Assets
            '1310' => '1000', // Inventory Adjustment Gain -> Current Assets
            '2100' => '2000', // Accounts Payable -> Current Liabilities
            '2200' => '2000', // Salaries Payable -> Current Liabilities
            '2210' => '2000', // EPF Payable -> Current Liabilities
            '2220' => '2000', // ETF Payable -> Current Liabilities
            '3100' => '3000', // Capital -> Owner's Equity
            '3200' => '3000', // Retained Earnings -> Owner's Equity
            '4100' => '4000', // Product Sales -> Sales Revenue
            '4200' => '4000', // Sales Returns -> Sales Revenue
            '5100' => '5000', // Purchases -> COGS
            '6100' => '6000', // Salaries -> Operating Expenses
            '6200' => '6000', // Rent -> Operating Expenses
            '6300' => '6000', // Utilities -> Operating Expenses
            '6400' => '6000', // Office Supplies -> Operating Expenses
            '6500' => '6000', // Marketing -> Operating Expenses
            '6600' => '6000', // Maintenance -> Operating Expenses
            '6700' => '6000', // Transportation -> Operating Expenses
            '6800' => '6000', // Professional Fees -> Operating Expenses
            '6900' => '6000', // Miscellaneous -> Operating Expenses
            '7100' => '6000', // Inventory Loss/Shrinkage -> Operating Expenses
        ];

        foreach ($parentMappings as $childCode => $parentCode) {
            $child = Account::where('account_code', $childCode)->first();
            $parent = Account::where('account_code', $parentCode)->first();

            if ($child && $parent) {
                $child->update(['parent_account_id' => $parent->id]);
            }
        }
    }
}
