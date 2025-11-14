<?php

namespace Database\Seeders;

use App\Models\FiscalPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FiscalPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = Carbon::now()->year;

        // Create fiscal periods for current year (12 months)
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

            FiscalPeriod::create([
                'name' => $startDate->format('F Y'),
                'year' => $currentYear,
                'month' => $month,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'open',
                'closed_by' => null,
                'closed_at' => null,
            ]);
        }
    }
}
