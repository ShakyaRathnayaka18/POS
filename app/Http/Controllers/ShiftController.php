<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClockInRequest;
use App\Http\Requests\ClockOutRequest;
use App\Models\Shift;
use App\Services\ShiftService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function __construct(protected ShiftService $shiftService) {}

    /**
     * Display a listing of all shifts (admin/manager view).
     */
    public function index(Request $request): View
    {
        $query = Shift::with('user')
            ->orderBy('clock_in_at', 'desc');

        // Filter by user if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->filled('from_date')) {
            $query->whereDate('clock_in_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('clock_in_at', '<=', $request->to_date);
        }

        $shifts = $query->paginate(15);

        return view('shifts.index', compact('shifts'));
    }

    /**
     * Display cashier's own shift history.
     */
    public function userShifts(): View
    {
        $shifts = Shift::where('user_id', auth()->id())
            ->with('sales')
            ->orderBy('clock_in_at', 'desc')
            ->paginate(15);

        return view('shifts.my-shifts', compact('shifts'));
    }

    /**
     * Get current active shift for the authenticated user (API endpoint).
     */
    public function current(): JsonResponse
    {
        $activeShift = $this->shiftService->getCurrentActiveShift(auth()->id());

        if (! $activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'No active shift found.',
                'data' => null,
            ]);
        }

        $stats = $this->shiftService->getShiftStatistics($activeShift->id);

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => $activeShift,
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Clock in a cashier and start a new shift.
     */
    public function clockIn(ClockInRequest $request): JsonResponse
    {
        try {
            $shift = $this->shiftService->clockIn(
                auth()->id(),
                $request->opening_cash,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Clocked in successfully.',
                'data' => ['shift' => $shift],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Clock out a cashier and complete their shift.
     */
    public function clockOut(ClockOutRequest $request, Shift $shift): JsonResponse
    {
        // Ensure the shift belongs to the authenticated user
        if ($shift->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        try {
            $completedShift = $this->shiftService->clockOut(
                $shift->id,
                $request->closing_cash,
                $request->notes
            );

            $stats = $this->shiftService->getShiftStatistics($completedShift->id);

            return response()->json([
                'success' => true,
                'message' => 'Clocked out successfully.',
                'data' => [
                    'shift' => $completedShift,
                    'statistics' => $stats,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified shift.
     */
    public function show(Shift $shift): View
    {
        // Check authorization
        if (auth()->user()->cannot('view shifts') && $shift->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $shift->load(['user', 'sales.items']);
        $statistics = $this->shiftService->getShiftStatistics($shift->id);

        return view('shifts.show', compact('shift', 'statistics'));
    }

    /**
     * Approve a completed shift (manager/admin only).
     */
    public function approve(Shift $shift): RedirectResponse
    {
        try {
            $this->shiftService->approveShift($shift->id);

            return redirect()->back()
                ->with('success', 'Shift approved successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
