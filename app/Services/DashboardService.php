<?php

namespace App\Services;

use App\Enums\ShiftStatusEnum;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\SupplierCredit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get today's total sales revenue
     */
    public function getTodaysSales(?string $asOfDate = null): float
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate);

            return Sale::whereDate('created_at', $targetDate)->sum('total');
        }

        return Cache::remember('dashboard.todays_sales', 300, function () {
            return Sale::whereDate('created_at', today())->sum('total');
        });
    }

    /**
     * Get out of stock products with details
     */
    public function getOutOfStockItems(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Get products that existed at that time
            $products = Product::where('created_at', '<=', $targetDate)
                ->with(['category', 'brand'])
                ->get()
                ->filter(function ($product) use ($targetDate) {
                    // Calculate historical stock for this product
                    $historicalStock = $this->calculateHistoricalStock($product->id, $targetDate);

                    return $historicalStock <= 0;
                });

            return [
                'count' => $products->count(),
                'products' => $products,
            ];
        }

        return Cache::remember('dashboard.out_of_stock', 600, function () {
            $products = Product::whereDoesntHave('availableStocks', function ($q) {
                $q->where('available_quantity', '>', 0);
            })
                ->with(['category', 'brand'])
                ->get();

            return [
                'count' => $products->count(),
                'products' => $products,
            ];
        });
    }

    /**
     * Calculate historical stock level for a product at a specific date
     */
    protected function calculateHistoricalStock(int $productId, \Carbon\Carbon $targetDate): int
    {
        // Current stock
        $currentStock = DB::table('stocks')
            ->where('product_id', $productId)
            ->sum('available_quantity');

        // Sales after target date
        $salesAfter = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sale_items.product_id', $productId)
            ->where('sales.created_at', '>', $targetDate)
            ->sum('sale_items.quantity');

        // GRNs/Stock additions after target date
        $stocksAfter = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('created_at', '>', $targetDate)
            ->sum('quantity');

        // Historical stock = current - additions after + sales after
        return $currentStock - $stocksAfter + $salesAfter;
    }

    /**
     * Get top selling products for a period
     *
     * @param  string  $period  'today'|'week'|'month'
     */
    public function getTopSellingProducts(string $period = 'today', ?string $asOfDate = null): \Illuminate\Support\Collection
    {
        [$startDate, $endDate] = $this->getPeriodRange($period, $asOfDate);

        if ($asOfDate) {
            return DB::table('sale_items')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->select('products.product_name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
                ->groupBy('products.id', 'products.product_name')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        }

        return Cache::remember("dashboard.top_selling_products.{$period}", 600, function () use ($startDate, $endDate) {
            return DB::table('sale_items')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->select('products.product_name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
                ->groupBy('products.id', 'products.product_name')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Get batches expiring in 30, 60, 90 days
     */
    public function getExpiringBatches(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate);

            return [
                'days_30' => Batch::where('created_at', '<=', $targetDate->copy()->endOfDay())
                    ->whereBetween('expiry_date', [$targetDate, $targetDate->copy()->addDays(30)])
                    ->with('product')
                    ->count(),
                'days_60' => Batch::where('created_at', '<=', $targetDate->copy()->endOfDay())
                    ->whereBetween('expiry_date', [$targetDate, $targetDate->copy()->addDays(60)])
                    ->with('product')
                    ->count(),
                'days_90' => Batch::where('created_at', '<=', $targetDate->copy()->endOfDay())
                    ->whereBetween('expiry_date', [$targetDate, $targetDate->copy()->addDays(90)])
                    ->with('product')
                    ->count(),
                'batches' => Batch::where('created_at', '<=', $targetDate->copy()->endOfDay())
                    ->whereBetween('expiry_date', [$targetDate, $targetDate->copy()->addDays(90)])
                    ->with(['product.category', 'product.brand'])
                    ->orderBy('expiry_date', 'asc')
                    ->get(),
            ];
        }

        return Cache::remember('dashboard.expiring_batches', 900, function () {
            return [
                'days_30' => Batch::whereBetween('expiry_date', [now(), now()->addDays(30)])
                    ->with('product')
                    ->count(),
                'days_60' => Batch::whereBetween('expiry_date', [now(), now()->addDays(60)])
                    ->with('product')
                    ->count(),
                'days_90' => Batch::whereBetween('expiry_date', [now(), now()->addDays(90)])
                    ->with('product')
                    ->count(),
                'batches' => Batch::whereBetween('expiry_date', [now(), now()->addDays(90)])
                    ->with(['product.category', 'product.brand'])
                    ->orderBy('expiry_date', 'asc')
                    ->get(),
            ];
        });
    }

    /**
     * Calculate profit margin percentage
     */
    public function getProfitMargin(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Get total revenue from Sales Revenue account (4100)
            $revenue = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('journal_entries.status', 'posted')
                ->where('journal_entries.created_at', '<=', $targetDate)
                ->where('accounts.account_code', '4100')
                ->sum('journal_entry_lines.credit_amount');

            // Get total COGS from COGS account (5100)
            $cogs = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('journal_entries.status', 'posted')
                ->where('journal_entries.created_at', '<=', $targetDate)
                ->where('accounts.account_code', '5100')
                ->sum('journal_entry_lines.debit_amount');

            if ($revenue <= 0) {
                return [
                    'percentage' => 0,
                    'amount' => 0
                ];
            }

            return [
                'percentage' => round((($revenue - $cogs) / $revenue) * 100, 2),
                'amount' => $revenue - $cogs
            ];
        }

        return Cache::remember('dashboard.profit_margin', 900, function () {
            // Get total revenue from Sales Revenue account (4100)
            $revenue = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('journal_entries.status', 'posted')
                ->where('accounts.account_code', '4100')
                ->sum('journal_entry_lines.credit_amount');

            // Get total COGS from COGS account (5100)
            $cogs = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('journal_entries.status', 'posted')
                ->where('accounts.account_code', '5100')
                ->sum('journal_entry_lines.debit_amount');

            if ($revenue <= 0) {
                return [
                    'percentage' => 0,
                    'amount' => 0
                ];
            }

            return [
                'percentage' => round((($revenue - $cogs) / $revenue) * 100, 2),
                'amount' => $revenue - $cogs
            ];
        });
    }

    /**
     * Get total outstanding customer credits
     */
    public function getOutstandingCustomerCredits(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Get credits that existed at target date
            $credits = CustomerCredit::where('created_at', '<=', $targetDate)
                ->with('customer')
                ->orderBy('due_date', 'asc')
                ->get()
                ->filter(function ($credit) use ($targetDate) {
                    // Check if credit was paid after target date
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    // Calculate outstanding amount as of target date
                    $historicalOutstanding = $credit->outstanding_amount + $paidAfter;

                    return $historicalOutstanding > 0;
                });

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum(function ($credit) use ($targetDate) {
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    return $credit->outstanding_amount + $paidAfter;
                }),
                'credits' => $credits,
            ];
        }

        return Cache::remember('dashboard.customer_credits', 600, function () {
            $credits = CustomerCredit::whereNotIn('status', ['paid'])
                ->with('customer')
                ->orderBy('due_date', 'asc')
                ->get();

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum('outstanding_amount'),
                'credits' => $credits,
            ];
        });
    }

    /**
     * Get total outstanding supplier credits
     */
    public function getOutstandingSupplierCredits(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Get credits that existed at target date
            $credits = SupplierCredit::where('created_at', '<=', $targetDate)
                ->with('supplier')
                ->orderBy('due_date', 'asc')
                ->get()
                ->filter(function ($credit) use ($targetDate) {
                    // Check if credit was paid after target date
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    // Calculate outstanding amount as of target date
                    $historicalOutstanding = $credit->outstanding_amount + $paidAfter;

                    return $historicalOutstanding > 0;
                });

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum(function ($credit) use ($targetDate) {
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    return $credit->outstanding_amount + $paidAfter;
                }),
                'credits' => $credits,
            ];
        }

        return Cache::remember('dashboard.supplier_credits', 600, function () {
            $credits = SupplierCredit::whereNotIn('status', ['paid'])
                ->with('supplier')
                ->orderBy('due_date', 'asc')
                ->get();

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum('outstanding_amount'),
                'credits' => $credits,
            ];
        });
    }

    /**
     * Get count of active customers
     */
    public function getActiveCustomersCount(?string $asOfDate = null): int
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            return Customer::where('is_active', true)
                ->where('created_at', '<=', $targetDate)
                ->count();
        }

        return Cache::remember('dashboard.active_customers', 900, function () {
            return Customer::where('is_active', true)->count();
        });
    }

    /**
     * Get total count of products
     */
    public function getTotalProductsCount(?string $asOfDate = null): int
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            return Product::where('created_at', '<=', $targetDate)->count();
        }

        return Cache::remember('dashboard.total_products', 900, function () {
            return Product::count();
        });
    }

    /**
     * Get overdue customer credits
     */
    public function getOverdueCustomerCredits(?string $asOfDate = null): array
    {
        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Get credits that were overdue at target date
            $credits = CustomerCredit::where('created_at', '<=', $targetDate)
                ->where('due_date', '<', $targetDate)
                ->with('customer')
                ->get()
                ->filter(function ($credit) use ($targetDate) {
                    // Check if credit was paid after target date
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    // Calculate outstanding amount as of target date
                    $historicalOutstanding = $credit->outstanding_amount + $paidAfter;

                    return $historicalOutstanding > 0;
                });

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum(function ($credit) use ($targetDate) {
                    $paidAfter = $credit->payments()
                        ->where('created_at', '>', $targetDate)
                        ->sum('amount');

                    return $credit->outstanding_amount + $paidAfter;
                }),
                'credits' => $credits,
            ];
        }

        return Cache::remember('dashboard.overdue_credits', 600, function () {
            $credits = CustomerCredit::where('due_date', '<', now())
                ->whereNotIn('status', ['paid'])
                ->with('customer')
                ->get();

            return [
                'count' => $credits->count(),
                'total_amount' => $credits->sum('outstanding_amount'),
                'credits' => $credits,
            ];
        });
    }

    /**
     * Get currently active shifts
     */
    public function getActiveShifts(): Collection
    {
        return Shift::where('status', ShiftStatusEnum::ACTIVE)
            ->with('user')
            ->get();
    }

    /**
     * Get profit data over time with both gross and net profit
     *
     * @param  string  $period  'daily'|'monthly'|'custom'
     * @param  string|null  $startDate  For custom range (Y-m-d format)
     * @param  string|null  $endDate  For custom range (Y-m-d format)
     * @return array ['labels' => [...], 'gross_profit' => [...], 'net_profit' => [...]]
     */
    public function getProfitOverTime(string $period = 'daily', ?string $startDate = null, ?string $endDate = null, ?string $asOfDate = null): array
    {
        [$start, $end, $grouping] = $this->determineProfitPeriodAndGrouping($period, $startDate, $endDate, $asOfDate);

        if ($asOfDate) {
            $targetDate = \Carbon\Carbon::parse($asOfDate)->endOfDay();

            // Query journal entries grouped by time period
            $data = DB::table('journal_entries as je')
                ->join('journal_entry_lines as jel', 'je.id', '=', 'jel.journal_entry_id')
                ->join('accounts as a', 'jel.account_id', '=', 'a.id')
                ->where('je.status', 'posted')
                ->where('je.created_at', '<=', $targetDate)
                ->whereBetween('je.entry_date', [$start, $end])
                ->whereIn('a.account_code', ['4100', '5100', '6100']) // Revenue, COGS, Operating Expenses
                ->select(
                    DB::raw($this->getGroupByExpression($grouping, 'je.entry_date') . ' as period'),
                    'a.account_code',
                    DB::raw('SUM(jel.credit_amount) as total_credit'),
                    DB::raw('SUM(jel.debit_amount) as total_debit')
                )
                ->groupBy('period', 'a.account_code')
                ->orderBy('period')
                ->get();

            return $this->formatProfitDataForChart($data, $start, $end, $grouping);
        }

        $cacheKey = $period === 'custom'
            ? "dashboard.profit_over_time.custom.{$startDate}.{$endDate}"
            : "dashboard.profit_over_time.{$period}";

        return Cache::remember($cacheKey, 600, function () use ($start, $end, $grouping) {
            // Query journal entries grouped by time period
            $data = DB::table('journal_entries as je')
                ->join('journal_entry_lines as jel', 'je.id', '=', 'jel.journal_entry_id')
                ->join('accounts as a', 'jel.account_id', '=', 'a.id')
                ->where('je.status', 'posted')
                ->whereBetween('je.entry_date', [$start, $end])
                ->whereIn('a.account_code', ['4100', '5100', '6100']) // Revenue, COGS, Operating Expenses
                ->select(
                    DB::raw($this->getGroupByExpression($grouping, 'je.entry_date') . ' as period'),
                    'a.account_code',
                    DB::raw('SUM(jel.credit_amount) as total_credit'),
                    DB::raw('SUM(jel.debit_amount) as total_debit')
                )
                ->groupBy('period', 'a.account_code')
                ->orderBy('period')
                ->get();

            return $this->formatProfitDataForChart($data, $start, $end, $grouping);
        });
    }

    /**
     * Determine date range and grouping for profit calculation
     */
    protected function determineProfitPeriodAndGrouping(string $period, ?string $startDate, ?string $endDate, ?string $asOfDate = null): array
    {
        $referenceDate = $asOfDate ? \Carbon\Carbon::parse($asOfDate) : now();

        if ($period === 'custom' && $startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();

            // Auto-determine grouping based on date range
            $daysDiff = $start->diffInDays($end);

            if ($daysDiff <= 7) {
                $grouping = 'daily';
            } elseif ($daysDiff <= 60) {
                $grouping = 'weekly';
            } else {
                $grouping = 'monthly';
            }

            return [$start, $end, $grouping];
        }

        // Preset periods
        return match ($period) {
            'daily' => [$referenceDate->copy()->subDays(6)->startOfDay(), $referenceDate->copy()->endOfDay(), 'daily'],
            'monthly' => [$referenceDate->copy()->subMonths(11)->startOfMonth(), $referenceDate->copy()->endOfMonth(), 'monthly'],
            default => [$referenceDate->copy()->subDays(6)->startOfDay(), $referenceDate->copy()->endOfDay(), 'daily'],
        };
    }

    /**
     * Get SQL expression for grouping by period
     */
    protected function getGroupByExpression(string $grouping, string $dateColumn): string
    {
        return match ($grouping) {
            'daily' => "DATE({$dateColumn})",
            'weekly' => "DATE_FORMAT({$dateColumn}, '%Y-%u')",
            'monthly' => "DATE_FORMAT({$dateColumn}, '%Y-%m')",
            default => "DATE({$dateColumn})",
        };
    }

    /**
     * Format raw data into chart-ready arrays
     */
    protected function formatProfitDataForChart($data, $start, $end, string $grouping): array
    {
        $labels = $this->generatePeriodLabels($start, $end, $grouping);
        $grossProfit = array_fill(0, count($labels), 0);
        $netProfit = array_fill(0, count($labels), 0);

        // Group data by period
        $periodData = [];
        foreach ($data as $row) {
            $period = $row->period;
            if (! isset($periodData[$period])) {
                $periodData[$period] = ['revenue' => 0, 'cogs' => 0, 'expenses' => 0];
            }

            if ($row->account_code === '4100') {
                $periodData[$period]['revenue'] += $row->total_credit;
            } elseif ($row->account_code === '5100') {
                $periodData[$period]['cogs'] += $row->total_debit;
            } elseif ($row->account_code === '6100') {
                $periodData[$period]['expenses'] += $row->total_debit;
            }
        }

        // Map to chart arrays
        foreach ($periodData as $period => $values) {
            $index = array_search($this->getPeriodKey($period, $grouping), $labels);
            if ($index !== false) {
                $grossProfit[$index] = round($values['revenue'] - $values['cogs'], 2);
                $netProfit[$index] = round($values['revenue'] - $values['cogs'] - $values['expenses'], 2);
            }
        }

        return [
            'labels' => $labels,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Generate all period labels for the date range
     */
    protected function generatePeriodLabels($start, $end, string $grouping): array
    {
        $labels = [];
        $current = $start->copy();

        while ($current <= $end) {
            $labels[] = match ($grouping) {
                'daily' => $current->format('M d'),
                'weekly' => 'Week ' . $current->format('W, Y'),
                'monthly' => $current->format('M Y'),
                default => $current->format('M d'),
            };

            $current = match ($grouping) {
                'daily' => $current->addDay(),
                'weekly' => $current->addWeek(),
                'monthly' => $current->addMonth(),
                default => $current->addDay(),
            };
        }

        return $labels;
    }

    /**
     * Convert database period format to label format
     */
    protected function getPeriodKey(string $period, string $grouping): string
    {
        if ($grouping === 'weekly') {
            // Extract year and week from '2025-46' format
            [$year, $week] = explode('-', $period);

            return "Week {$week}, {$year}";
        }

        return match ($grouping) {
            'daily' => \Carbon\Carbon::parse($period)->format('M d'),
            'monthly' => \Carbon\Carbon::parse($period . '-01')->format('M Y'),
            default => $period,
        };
    }

    /**
     * Get date range for period filter
     */
    protected function getPeriodRange(string $period, ?string $asOfDate = null): array
    {
        $referenceDate = $asOfDate ? \Carbon\Carbon::parse($asOfDate) : now();

        return match ($period) {
            'today' => [$referenceDate->copy()->startOfDay(), $referenceDate->copy()->endOfDay()],
            'week' => [$referenceDate->copy()->startOfWeek(), $referenceDate->copy()->endOfWeek()],
            'month' => [$referenceDate->copy()->startOfMonth(), $referenceDate->copy()->endOfMonth()],
            default => [$referenceDate->copy()->startOfDay(), $referenceDate->copy()->endOfDay()],
        };
    }

    /**
     * Clear all dashboard cache
     */
    public function clearCache(): void
    {
        $keys = [
            'dashboard.todays_sales',
            'dashboard.out_of_stock',
            'dashboard.top_selling_products.*',
            'dashboard.expiring_batches',
            'dashboard.profit_margin',
            'dashboard.customer_credits',
            'dashboard.supplier_credits',
            'dashboard.active_customers',
            'dashboard.total_products',
            'dashboard.overdue_credits',
            'dashboard.profit_over_time.*',
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // Clear pattern-based keys
                $pattern = str_replace('*', '', $key);
                Cache::forget($pattern . 'daily');
                Cache::forget($pattern . 'monthly');
                // Custom date ranges would have unique keys
            } else {
                Cache::forget($key);
            }
        }
    }
}
