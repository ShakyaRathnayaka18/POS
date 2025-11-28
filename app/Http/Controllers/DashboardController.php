<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        $asOfDate = $request->query('as_of_date');

        // Validate date format if provided
        if ($asOfDate) {
            $request->validate([
                'as_of_date' => 'date|before_or_equal:today',
            ]);
        }

        // Get initial data for page load
        $data = [
            'todaysSales' => $this->dashboardService->getTodaysSales($asOfDate),
            'outOfStock' => $this->dashboardService->getOutOfStockItems($asOfDate),
            'topSellingProducts' => $this->dashboardService->getTopSellingProducts('today', $asOfDate),
            'expiringBatches' => $this->dashboardService->getExpiringBatches($asOfDate),
            'profitMargin' => $this->dashboardService->getProfitMargin($asOfDate),
            'customerCredits' => $this->dashboardService->getOutstandingCustomerCredits($asOfDate),
            'supplierCredits' => $this->dashboardService->getOutstandingSupplierCredits($asOfDate),
            'activeCustomers' => $this->dashboardService->getActiveCustomersCount($asOfDate),
            'overdueCredits' => $this->dashboardService->getOverdueCustomerCredits($asOfDate),
            'activeShifts' => $this->dashboardService->getActiveShifts(),
            'profitData' => $this->dashboardService->getProfitOverTime('daily', null, null, $asOfDate),
        ];

        $data['asOfDate'] = $asOfDate;

        return view('admin.dashboard', $data);
    }

    /**
     * API endpoint for top selling products with period filter
     */
    public function topSellingProducts(Request $request)
    {
        $validated = $request->validate([
            'period' => ['required', Rule::in(['today', 'week', 'month'])],
            'as_of_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $data = $this->dashboardService->getTopSellingProducts(
            $validated['period'],
            $validated['as_of_date'] ?? null
        );

        return response()->json($data);
    }

    /**
     * API endpoint for profit chart with period filter
     */
    public function profitData(Request $request)
    {
        $validated = $request->validate([
            'period' => ['required', Rule::in(['daily', 'monthly', 'custom'])],
            'start_date' => ['required_if:period,custom', 'nullable', 'date'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date', 'after_or_equal:start_date'],
            'as_of_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $data = $this->dashboardService->getProfitOverTime(
            $validated['period'],
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null,
            $validated['as_of_date'] ?? null
        );

        return response()->json($data);
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        $this->dashboardService->clearCache();

        return response()->json(['message' => 'Dashboard cache cleared successfully']);
    }
}
