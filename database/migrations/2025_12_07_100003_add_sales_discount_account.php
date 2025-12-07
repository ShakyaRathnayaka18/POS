<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\AccountType;

return new class extends Migration
{
    public function up(): void
    {
        $revenueTypeId = AccountType::where('name', 'Revenue')->first()?->id;
        $salesRevenueAccount = Account::where('account_code', '4100')->first();

        if ($revenueTypeId && $salesRevenueAccount) {
            Account::create([
                'account_code' => '4200',
                'account_name' => 'Sales Discounts',
                'account_type_id' => $revenueTypeId,
                'parent_id' => $salesRevenueAccount->id,
                'is_active' => true,
                'description' => 'Contra-revenue account for tracking sales discounts',
            ]);
        }
    }

    public function down(): void
    {
        Account::where('account_code', '4200')->delete();
    }
};
