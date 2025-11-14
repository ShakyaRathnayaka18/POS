<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $accountTypes = [
            [
                'name' => 'Asset',
                'normal_balance' => 'debit',
                'category' => 'Balance Sheet',
            ],
            [
                'name' => 'Liability',
                'normal_balance' => 'credit',
                'category' => 'Balance Sheet',
            ],
            [
                'name' => 'Equity',
                'normal_balance' => 'credit',
                'category' => 'Balance Sheet',
            ],
            [
                'name' => 'Revenue',
                'normal_balance' => 'credit',
                'category' => 'Income Statement',
            ],
            [
                'name' => 'Expense',
                'normal_balance' => 'debit',
                'category' => 'Income Statement',
            ],
        ];

        foreach ($accountTypes as $accountType) {
            AccountType::create($accountType);
        }
    }
}
