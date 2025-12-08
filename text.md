Claude’s Plan
Stock Adjustment Module Implementation Plan
Overview
Create a comprehensive Stock Adjustment Module that allows users to increase or decrease stock quantities with full accounting integration, audit trails, and a dedicated management interface.
User Requirements
Stock Adjustments: Ability to increase or decrease stock quantities
Accounting Integration: Reference adjustments in the accounting module automatically
Audit Panel: Dedicated view to see all stock adjustments with history
Enhanced Edit Modal: Allow quantity changes in the existing stocks index edit modal
Audit Trail: Show adjustments in dedicated panel (NOT in GRN notes)
Current System Architecture
Database
Stocks: product_id, batch_id, cost_price, selling_price, quantity, available_quantity (decimal:4)
Batches: Contains notes field (currently used for audit trail)
Journal Entries: Polymorphic reference system for accounting integration
Accounting Integration
Observer pattern: GRN, Sale, Expense, etc. trigger journal entries automatically
TransactionIntegrationService: Centralized service for creating accounting entries
Chart of Accounts: Account 1300 (Inventory), needs adjustment accounts (7100, 1310)
Stock Operations
FIFO allocation with FOC priority
Decimal quantity support (4 decimals)
Current audit via batch notes (will move to dedicated table)
Implementation Plan
Phase 1: Database Schema
Step 1.1: Create Stock Adjustments Migration
File: database/migrations/YYYY_MM_DD_create_stock_adjustments_table.php
Schema::create('stock_adjustments', function (Blueprint $table) {
    $table->id();
    $table->string('adjustment_number')->unique(); // Format: SA-YYYYMMDD-####
    $table->foreignId('stock_id')->constrained('stocks')->onDelete('restrict');
    $table->foreignId('product_id')->constrained('products'); // Denormalized for querying
    $table->foreignId('batch_id')->constrained('batches'); // Denormalized for querying
    $table->enum('type', ['increase', 'decrease']);
    $table->decimal('quantity_before', 10, 4);
    $table->decimal('quantity_adjusted', 10, 4); // Positive for both increase/decrease
    $table->decimal('quantity_after', 10, 4);
    $table->decimal('cost_price', 10, 2); // From stock record
    $table->decimal('total_value', 10, 2); // quantity_adjusted × cost_price
    $table->string('reason'); // Dropdown: damage, theft, recount, return_to_supplier, found, etc.
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamp('adjustment_date');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
});
Step 1.2: Add Inventory Adjustment Accounts to Chart of Accounts
File: Update database/seeders/ChartOfAccountsSeeder.php or create separate seeder Add these accounts:
7100 - Inventory Loss/Shrinkage (Expense account)
1310 - Inventory Gain/Adjustment (Contra-Asset account)
Account::create([
    'account_code' => '7100',
    'account_name' => 'Inventory Loss/Shrinkage',
    'account_type_id' => $expenseTypeId,
    'description' => 'Loss of inventory due to damage, theft, or obsolescence'
]);

Account::create([
    'account_code' => '1310',
    'account_name' => 'Inventory Adjustment Gain',
    'account_type_id' => $assetTypeId,
    'description' => 'Inventory increases from recounts or found items'
]);
Phase 2: Models and Relationships
Step 2.1: Create StockAdjustment Model
File: app/Models/StockAdjustment.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_number',
        'stock_id',
        'product_id',
        'batch_id',
        'type',
        'quantity_before',
        'quantity_adjusted',
        'quantity_after',
        'cost_price',
        'total_value',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'adjustment_date',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity_before' => 'decimal:4',
            'quantity_adjusted' => 'decimal:4',
            'quantity_after' => 'decimal:4',
            'cost_price' => 'decimal:2',
            'total_value' => 'decimal:2',
            'adjustment_date' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }

    // Helper methods
    public function isIncrease(): bool
    {
        return $this->type === 'increase';
    }

    public function isDecrease(): bool
    {
        return $this->type === 'decrease';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
Step 2.2: Update Stock Model
File: app/Models/Stock.php Add relationship:
public function adjustments()
{
    return $this->hasMany(StockAdjustment::class);
}
Phase 3: Services Layer
Step 3.1: Create StockAdjustmentService
File: app/Services/StockAdjustmentService.php
<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    /**
     * Generate unique adjustment number
     * Format: SA-YYYYMMDD-####
     */
    public function generateAdjustmentNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "SA-{$date}-";

        $lastAdjustment = StockAdjustment::where('adjustment_number', 'like', "{$prefix}%")
            ->orderBy('adjustment_number', 'desc')
            ->first();

        if ($lastAdjustment) {
            $lastNumber = (int) substr($lastAdjustment->adjustment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create stock adjustment (pending approval)
     */
    public function createAdjustment(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            $stock = Stock::with(['product', 'batch'])->findOrFail($data['stock_id']);

            // Calculate values
            $quantityBefore = $stock->available_quantity;
            $quantityAdjusted = abs($data['quantity_adjusted']);

            if ($data['type'] === 'increase') {
                $quantityAfter = $quantityBefore + $quantityAdjusted;
            } else {
                if ($quantityBefore < $quantityAdjusted) {
                    throw new \Exception("Cannot decrease more than available quantity ({$quantityBefore})");
                }
                $quantityAfter = $quantityBefore - $quantityAdjusted;
            }

            $totalValue = $quantityAdjusted * $stock->cost_price;

            // Create adjustment record
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $this->generateAdjustmentNumber(),
                'stock_id' => $stock->id,
                'product_id' => $stock->product_id,
                'batch_id' => $stock->batch_id,
                'type' => $data['type'],
                'quantity_before' => $quantityBefore,
                'quantity_adjusted' => $quantityAdjusted,
                'quantity_after' => $quantityAfter,
                'cost_price' => $stock->cost_price,
                'total_value' => $totalValue,
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'adjustment_date' => $data['adjustment_date'] ?? now(),
                'status' => 'pending',
            ]);

            return $adjustment->fresh(['stock.product', 'stock.batch', 'creator']);
        });
    }

    /**
     * Approve adjustment and update stock
     */
    public function approveAdjustment(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            if (!$adjustment->isPending()) {
                throw new \Exception('Only pending adjustments can be approved');
            }

            $stock = $adjustment->stock;

            // Update stock quantities
            if ($adjustment->isIncrease()) {
                $stock->increment('quantity', $adjustment->quantity_adjusted);
                $stock->increment('available_quantity', $adjustment->quantity_adjusted);
            } else {
                $stock->decrement('quantity', $adjustment->quantity_adjusted);
                $stock->decrement('available_quantity', $adjustment->quantity_adjusted);
            }

            // Update adjustment status
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Reject adjustment
     */
    public function rejectAdjustment(StockAdjustment $adjustment): void
    {
        if (!$adjustment->isPending()) {
            throw new \Exception('Only pending adjustments can be rejected');
        }

        $adjustment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }
}
Step 3.2: Add Method to TransactionIntegrationService
File: app/Services/TransactionIntegrationService.php Add this method:
/**
 * Create journal entry for stock adjustment
 */
public function createStockAdjustmentJournalEntry(StockAdjustment $adjustment): void
{
    $inventoryAccount = Account::where('account_code', '1300')->firstOrFail();

    if ($adjustment->isIncrease()) {
        // Inventory increase
        // Dr. Inventory (1300) / Cr. Inventory Gain (1310)
        $gainAccount = Account::where('account_code', '1310')->firstOrFail();

        $this->journalEntryService->createAndPostEntry([
            'entry_date' => $adjustment->adjustment_date,
            'description' => "Stock adjustment increase - {$adjustment->adjustment_number} ({$adjustment->reason})",
            'reference_type' => StockAdjustment::class,
            'reference_id' => $adjustment->id,
            'lines' => [
                [
                    'account_id' => $inventoryAccount->id,
                    'debit_amount' => $adjustment->total_value,
                    'credit_amount' => 0,
                    'description' => "Inventory increase - {$adjustment->product->product_name}",
                    'line_number' => 1,
                ],
                [
                    'account_id' => $gainAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $adjustment->total_value,
                    'description' => "Adjustment gain - {$adjustment->reason}",
                    'line_number' => 2,
                ],
            ],
        ]);
    } else {
        // Inventory decrease
        // Dr. Inventory Loss (7100) / Cr. Inventory (1300)
        $lossAccount = Account::where('account_code', '7100')->firstOrFail();

        $this->journalEntryService->createAndPostEntry([
            'entry_date' => $adjustment->adjustment_date,
            'description' => "Stock adjustment decrease - {$adjustment->adjustment_number} ({$adjustment->reason})",
            'reference_type' => StockAdjustment::class,
            'reference_id' => $adjustment->id,
            'lines' => [
                [
                    'account_id' => $lossAccount->id,
                    'debit_amount' => $adjustment->total_value,
                    'credit_amount' => 0,
                    'description' => "Inventory loss - {$adjustment->product->product_name} ({$adjustment->reason})",
                    'line_number' => 1,
                ],
                [
                    'account_id' => $inventoryAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $adjustment->total_value,
                    'description' => "Inventory reduction",
                    'line_number' => 2,
                ],
            ],
        ]);
    }
}
Note: Add createAndPostEntry() helper method if it doesn't exist:
protected function createAndPostEntry(array $data): JournalEntry
{
    $entry = $this->journalEntryService->createJournalEntry($data);
    $this->journalEntryService->postJournalEntry($entry);
    return $entry;
}
Phase 4: Observer Integration
Step 4.1: Create StockAdjustmentObserver
File: app/Observers/StockAdjustmentObserver.php
<?php

namespace App\Observers;

use App\Models\StockAdjustment;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class StockAdjustmentObserver
{
    public function __construct(
        protected TransactionIntegrationService $transactionIntegrationService
    ) {}

    /**
     * Handle the StockAdjustment "updated" event.
     * Create journal entry when status changes to 'approved'
     */
    public function updated(StockAdjustment $adjustment): void
    {
        // Only create journal entry when status changes to approved
        if ($adjustment->wasChanged('status') && $adjustment->isApproved()) {
            try {
                $this->transactionIntegrationService->createStockAdjustmentJournalEntry($adjustment);
            } catch (Exception $e) {
                Log::error('Failed to create journal entry for stock adjustment: '.$e->getMessage(), [
                    'adjustment_id' => $adjustment->id,
                    'adjustment_number' => $adjustment->adjustment_number,
                ]);
            }
        }
    }
}
Step 4.2: Register Observer
File: app/Providers/AppServiceProvider.php Add to boot() method:
use App\Models\StockAdjustment;
use App\Observers\StockAdjustmentObserver;

public function boot(): void
{
    // ... existing observers
    StockAdjustment::observe(StockAdjustmentObserver::class);
}
Phase 5: Form Requests
Step 5.1: Create StockAdjustmentRequest
File: app/Http/Requests/StockAdjustmentRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create stock adjustments');
    }

    public function rules(): array
    {
        return [
            'stock_id' => 'required|exists:stocks,id',
            'type' => 'required|in:increase,decrease',
            'quantity_adjusted' => 'required|numeric|min:0.0001|max:99999.9999',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'adjustment_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'stock_id.required' => 'Stock selection is required',
            'stock_id.exists' => 'Selected stock does not exist',
            'type.required' => 'Adjustment type is required',
            'type.in' => 'Adjustment type must be increase or decrease',
            'quantity_adjusted.required' => 'Adjustment quantity is required',
            'quantity_adjusted.numeric' => 'Quantity must be a number',
            'quantity_adjusted.min' => 'Quantity must be greater than 0',
            'quantity_adjusted.max' => 'Quantity cannot exceed 99999.9999',
            'reason.required' => 'Adjustment reason is required',
        ];
    }
}
Phase 6: Controllers
Step 6.1: Create StockAdjustmentController
File: app/Http/Controllers/StockAdjustmentController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\StockAdjustment;
use App\Models\Stock;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function __construct(
        protected StockAdjustmentService $adjustmentService
    ) {}

    /**
     * Display list of stock adjustments
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['stock.product', 'stock.batch', 'creator', 'approver'])
            ->latest('adjustment_date');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        $adjustments = $query->paginate(20);

        // Stats
        $stats = [
            'total' => StockAdjustment::count(),
            'pending' => StockAdjustment::where('status', 'pending')->count(),
            'approved' => StockAdjustment::where('status', 'approved')->count(),
            'rejected' => StockAdjustment::where('status', 'rejected')->count(),
            'total_value_increase' => StockAdjustment::where('status', 'approved')
                ->where('type', 'increase')
                ->sum('total_value'),
            'total_value_decrease' => StockAdjustment::where('status', 'approved')
                ->where('type', 'decrease')
                ->sum('total_value'),
        ];

        return view('stock-adjustments.index', compact('adjustments', 'stats'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $stock = null;
        if ($request->filled('stock_id')) {
            $stock = Stock::with(['product', 'batch'])->find($request->stock_id);
        }

        return view('stock-adjustments.create', compact('stock'));
    }

    /**
     * Store new adjustment
     */
    public function store(StockAdjustmentRequest $request)
    {
        try {
            $adjustment = $this->adjustmentService->createAdjustment($request->validated());

            return redirect()->route('stock-adjustments.show', $adjustment)
                ->with('success', "Stock adjustment {$adjustment->adjustment_number} created successfully and pending approval.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create adjustment: '.$e->getMessage());
        }
    }

    /**
     * Display adjustment details
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load([
            'stock.product.category',
            'stock.product.brand',
            'stock.batch.goodReceiveNote.supplier',
            'creator',
            'approver',
            'journalEntry.lines.account',
        ]);

        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    /**
     * Approve adjustment
     */
    public function approve(StockAdjustment $stockAdjustment)
    {
        try {
            $this->adjustmentService->approveAdjustment($stockAdjustment);

            return redirect()->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve adjustment: '.$e->getMessage());
        }
    }

    /**
     * Reject adjustment
     */
    public function reject(StockAdjustment $stockAdjustment)
    {
        try {
            $this->adjustmentService->rejectAdjustment($stockAdjustment);

            return redirect()->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject adjustment: '.$e->getMessage());
        }
    }
}
Step 6.2: Update StockController
File: app/Http/Controllers/StockController.php Add quantity adjustment to the update method:
public function update(Request $request, Stock $stock)
{
    // Prevent editing FOC stocks
    if ($stock->isFoc()) {
        return redirect()->back()
            ->with('error', 'FOC (Free of Charge) stocks cannot be edited. Only non-FOC stocks can be modified.');
    }

    $validated = $request->validate([
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'barcode' => 'nullable|string|max:255|unique:batches,barcode,'.$stock->batch_id,
        'quantity_adjustment' => 'nullable|numeric', // NEW: Can be positive or negative
        'adjustment_reason' => 'required_with:quantity_adjustment|string|max:255',
        'adjustment_notes' => 'nullable|string|max:1000',
    ]);

    DB::transaction(function () use ($request, $stock, $validated) {
        // Track old values for audit
        $oldCostPrice = $stock->cost_price;
        $oldSellingPrice = $stock->selling_price;
        $oldBarcode = $stock->batch->barcode;

        // Update stock-level fields
        $stock->update([
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
        ]);

        // Update batch-level barcode if changed
        if ($validated['barcode'] !== $oldBarcode) {
            $stock->batch->update(['barcode' => $validated['barcode']]);
        }

        // Handle quantity adjustment if provided
        if (isset($validated['quantity_adjustment']) && $validated['quantity_adjustment'] != 0) {
            $adjustmentService = app(StockAdjustmentService::class);

            $type = $validated['quantity_adjustment'] > 0 ? 'increase' : 'decrease';
            $quantity = abs($validated['quantity_adjustment']);

            $adjustmentService->createAdjustment([
                'stock_id' => $stock->id,
                'type' => $type,
                'quantity_adjusted' => $quantity,
                'reason' => $validated['adjustment_reason'],
                'notes' => $validated['adjustment_notes'] ?? null,
                'adjustment_date' => now(),
            ]);
        }

        // Add audit trail for price/barcode changes only (not quantity)
        $userName = auth()->user()->name;
        $timestamp = now()->format('Y-m-d H:i:s');
        $changes = [];

        if ($oldCostPrice != $validated['cost_price']) {
            $changes[] = "Cost Price: LKR {$oldCostPrice} → LKR {$validated['cost_price']}";
        }
        if ($oldSellingPrice != $validated['selling_price']) {
            $changes[] = "Selling Price: LKR {$oldSellingPrice} → LKR {$validated['selling_price']}";
        }
        if ($validated['barcode'] !== $oldBarcode) {
            $changes[] = "Barcode: {$oldBarcode} → {$validated['barcode']}";
        }

        if (! empty($changes)) {
            $auditNote = "\n[{$timestamp}] {$userName}: ".implode(', ', $changes);
            $stock->batch->update([
                'notes' => $stock->batch->notes.$auditNote,
            ]);

            // Also add to GRN notes (only for price/barcode changes)
            $grn = $stock->batch->goodReceiveNote;
            $grn->update([
                'notes' => $grn->notes.$auditNote." (Stock ID: {$stock->id}, Product: {$stock->product->product_name})",
            ]);
        }
    });

    return redirect()->route('stocks.index')
        ->with('success', 'Stock updated successfully.');
}
Phase 7: Routes
File: routes/web.php Add stock adjustment routes:
Route::middleware(['auth', 'permission:manage stock adjustments'])->group(function () {
    Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])
        ->name('stock-adjustments.index');
    Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])
        ->name('stock-adjustments.create');
    Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])
        ->name('stock-adjustments.store');
    Route::get('/stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'show'])
        ->name('stock-adjustments.show');
    Route::post('/stock-adjustments/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve'])
        ->name('stock-adjustments.approve');
    Route::post('/stock-adjustments/{stockAdjustment}/reject', [StockAdjustmentController::class, 'reject'])
        ->name('stock-adjustments.reject');
});
Phase 8: Views
Step 8.1: Update Stock Edit Modal
File: resources/views/stocks/index.blade.php Update the edit modal to include quantity adjustment fields:
<!-- Add after barcode field, before the info note (around line 263) -->
<div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
        Quantity Adjustment (Optional)
    </h4>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="quantity_adjustment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Adjustment Quantity
            </label>
            <input type="number" id="quantity_adjustment" name="quantity_adjustment" step="0.0001"
                placeholder="Enter positive or negative value"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Positive to increase, negative to decrease
            </p>
        </div>

        <div>
            <label for="adjustment_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Reason (Required if adjusting)
            </label>
            <select id="adjustment_reason" name="adjustment_reason"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Select reason...</option>
                <option value="Damage">Damage</option>
                <option value="Theft/Loss">Theft/Loss</option>
                <option value="Recount">Physical Recount</option>
                <option value="Return to Supplier">Return to Supplier</option>
                <option value="Found Item">Found Item</option>
                <option value="Expired">Expired</option>
                <option value="Other">Other</option>
            </select>
        </div>
    </div>

    <div class="mt-3">
        <label for="adjustment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Notes (Optional)
        </label>
        <textarea id="adjustment_notes" name="adjustment_notes" rows="2"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            placeholder="Additional notes about this adjustment..."></textarea>
    </div>
</div>

<!-- Update the info note to mention adjustments -->
<div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md p-3">
    <p class="text-xs text-yellow-800 dark:text-yellow-300">
        <i class="fas fa-info-circle mr-1"></i>
        Price/barcode changes will be logged in GRN records. Quantity adjustments will be tracked separately in the Stock Adjustments panel.
    </p>
</div>
Add JavaScript validation:
// Add inside the existing script tag
document.getElementById('editStockForm').addEventListener('submit', function(e) {
    const adjustment = document.getElementById('quantity_adjustment').value;
    const reason = document.getElementById('adjustment_reason').value;

    if (adjustment && adjustment != 0 && !reason) {
        e.preventDefault();
        alert('Please select a reason for the quantity adjustment');
        document.getElementById('adjustment_reason').focus();
        return false;
    }
});
Step 8.2: Create Stock Adjustments Index View
File: resources/views/stock-adjustments/index.blade.php
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Stock Adjustments</h1>
        <a href="{{ route('stock-adjustments.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors">
            <i class="fas fa-plus mr-2"></i>New Adjustment
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Adjustments</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-clipboard-list text-blue-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                </div>
                <i class="fas fa-clock text-yellow-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Value Increased</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        LKR {{ number_format($stats['total_value_increase'], 2) }}
                    </p>
                </div>
                <i class="fas fa-arrow-up text-green-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Value Decreased</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        LKR {{ number_format($stats['total_value_decrease'], 2) }}
                    </p>
                </div>
                <i class="fas fa-arrow-down text-red-500 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('stock-adjustments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search, Status, Type, Date filters... -->
        </form>
    </div>

    <!-- Adjustments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Adjustment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- Table rows... -->
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No adjustments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
@endsection
Step 8.3: Create Adjustment Detail View
File: resources/views/stock-adjustments/show.blade.php (Similar detailed view with approval/reject buttons for pending adjustments, journal entry details, etc.)