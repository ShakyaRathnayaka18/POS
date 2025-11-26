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
    public function index()
    {
        // Get initial data for page load
        $data = [
            'todaysSales' => $this->dashboardService->getTodaysSales(),
            'outOfStock' => $this->dashboardService->getOutOfStockItems(),
            'topSellingProducts' => $this->dashboardService->getTopSellingProducts('today'),
            'expiringBatches' => $this->dashboardService->getExpiringBatches(),
            'profitMargin' => $this->dashboardService->getProfitMargin(),
            'customerCredits' => $this->dashboardService->getOutstandingCustomerCredits(),
            'supplierCredits' => $this->dashboardService->getOutstandingSupplierCredits(),
            'activeCustomers' => $this->dashboardService->getActiveCustomersCount(),
            'overdueCredits' => $this->dashboardService->getOverdueCustomerCredits(),
            'activeShifts' => $this->dashboardService->getActiveShifts(),
            'profitData' => $this->dashboardService->getProfitOverTime('daily'),
        ];

        return view('admin.dashboard', $data);
    }

    /**
     * API endpoint for top selling products with period filter
     */
    public function topSellingProducts(Request $request)
    {
        $validated = $request->validate([
            'period' => ['required', Rule::in(['today', 'week', 'month'])],
        ]);

        $data = $this->dashboardService->getTopSellingProducts($validated['period']);

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
        ]);

        $data = $this->dashboardService->getProfitOverTime(
            $validated['period'],
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
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
