# Returns Implementation Guide

**Version:** 1.0
**Last Updated:** November 8, 2025
**System:** POS Inventory Management System

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Current Inventory Architecture](#2-current-inventory-architecture)
3. [Supplier Returns Implementation Plan](#3-supplier-returns-implementation-plan)
4. [Sales Returns Implementation Plan](#4-sales-returns-implementation-plan)
5. [Testing Requirements](#5-testing-requirements)
6. [UI/UX Guidelines](#6-uiux-guidelines)

---

## 1. System Overview

This guide provides a step-by-step implementation plan for both **Supplier Returns** (returning products to suppliers) and **Sales Returns** (customers returning purchased products) in the POS system.

### 1.1 Key Concepts

- **Supplier Return**: Process of returning received inventory back to the supplier
- **Sales Return**: Process of accepting product returns from customers
- **FIFO (First In First Out)**: Inventory allocation strategy used in sales
- **Batch**: Container for products received in a single GRN
- **Stock**: Individual inventory entries linked to batches

### 1.2 Return Capabilities

**Supplier Returns Can:**
- Return one or multiple products from a GRN
- Return an entire batch
- Return partial quantities
- Track return reasons and financial adjustments

**Sales Returns Can:**
- Accept full or partial customer returns
- Restore inventory (for non-damaged goods)
- Process refunds or store credit
- Track return reasons and condition

---

## 2. Current Inventory Architecture

### 2.1 Inventory Flow

The system follows this flow for inventory management:

```
Purchase Order
    ↓
GRN (Good Receive Note) ← Header record from supplier
    ↓
BATCH ← One batch per product per GRN
    ↓
STOCK ← Actual inventory with pricing and quantities
    ↓
SALE ← Customer purchases (FIFO allocation)
```

### 2.2 Existing Database Tables

#### Table: `good_receive_notes`
**Purpose**: Header record for goods received from supplier

**Fields**:
- `id` - Primary key (BIGINT, auto-increment)
- `grn_number` - Unique identifier (VARCHAR 191, format: GRN-000001)
- `supplier_id` - Foreign key to suppliers
- `received_date` - Date received (DATE, nullable)
- `notes` - Optional notes (TEXT)
- `subtotal` - Subtotal amount (DECIMAL 15,2, default 0.00)
- `tax` - Tax amount (DECIMAL 15,2, default 0.00)
- `shipping` - Shipping cost (DECIMAL 15,2, default 0.00)
- `total` - Total amount (DECIMAL 15,2, default 0.00)
- `status` - Current status (VARCHAR 191, default 'Draft') - values: Draft, Received
- `created_at`, `updated_at` - Timestamps

**Relationships**:
- Belongs to one Supplier
- Has many Batches

#### Table: `batches`
**Purpose**: Groups inventory by receipt date and product

**Fields**:
- `id` - Primary key
- `batch_number` - Unique identifier (VARCHAR 191, format: SKU-M/D/Y)
- `barcode` - Optional barcode (VARCHAR 191, unique, nullable)
- `good_receive_note_id` - Foreign key (ON DELETE CASCADE)
- `manufacture_date` - Manufacturing date (DATE, nullable)
- `expiry_date` - Expiration date (DATE, nullable)
- `notes` - Additional notes (TEXT, nullable)
- `created_at`, `updated_at` - Timestamps

**Important**: Deleting a GRN cascades to delete all its batches

#### Table: `stocks`
**Purpose**: Actual inventory entries with pricing and quantity tracking

**Fields**:
- `id` - Primary key
- `product_id` - Foreign key (ON DELETE CASCADE)
- `batch_id` - Foreign key (ON DELETE CASCADE)
- `cost_price` - Purchase cost (DECIMAL 10,2)
- `selling_price` - Sale price (DECIMAL 10,2)
- `tax` - Tax percentage (DECIMAL 5,2, default 0.00)
- `quantity` - Original quantity received (INT, default 0) - **NEVER CHANGES**
- `available_quantity` - Current available stock (INT, default 0) - **DECREMENTS ON SALE**
- `created_at`, `updated_at` - Timestamps

**Critical Fields**:
- `quantity`: Permanent audit record of original amount
- `available_quantity`: Current sellable stock (can be restored on returns)

### 2.3 Model Relationships

```
GoodReceiveNote
  ├─ belongsTo(Supplier)
  └─ hasMany(Batch)

Batch
  ├─ belongsTo(GoodReceiveNote)
  └─ hasMany(Stock)

Stock
  ├─ belongsTo(Product)
  └─ belongsTo(Batch)

Product
  ├─ belongsTo(Category)
  ├─ belongsTo(Brand)
  ├─ hasMany(Stock)
  └─ belongsToMany(Supplier) with pivot fields: vendor_product_code, vendor_cost_price, is_preferred, lead_time_days

Supplier
  ├─ hasMany(GoodReceiveNote)
  └─ belongsToMany(Product)
```

### 2.4 Existing Services

**Location**: `app/Services/`

**GoodReceiveNoteService**:
- Generates sequential GRN numbers (format: GRN-000001)
- Creates GRN with batches and stocks in database transaction

**StockService**:
- Returns available stock using FIFO (oldest first)
- Allocates stock for sales using FIFO
- Decrements available_quantity on sales
- Calculates total available quantity per product

**BatchService**:
- Gets all batches for a product
- Finds batches expiring soon
- Calculates available quantities per batch

---

## 3. Supplier Returns Implementation Plan

### 3.1 Overview

Supplier returns handle returning inventory back to suppliers for reasons such as:
- Damaged/defective products
- Wrong items received
- Overstocking
- Quality issues

### 3.2 Return Scenarios to Support

1. **Single Product Return**: Return specific quantity of one product
2. **Multiple Product Return**: Return several products from same GRN
3. **Full Batch Return**: Return everything in a specific batch
4. **Partial Batch Return**: Return some quantity from a batch

### 3.3 Step 1: Create Database Tables

#### Table 1: `supplier_returns`

**Purpose**: Header record for supplier return transactions

**Fields to Create**:
- `id` - BIGINT primary key, auto-increment
- `return_number` - VARCHAR(191), unique, NOT NULL (format: SR-000001)
- `good_receive_note_id` - BIGINT foreign key to good_receive_notes, NOT NULL
- `supplier_id` - BIGINT foreign key to suppliers, NOT NULL
- `return_date` - DATE, NOT NULL
- `return_reason` - VARCHAR(191), NOT NULL (Damaged, Wrong Item, Defective, Overstocked)
- `notes` - TEXT, nullable
- `subtotal` - DECIMAL(15,2), default 0.00
- `tax` - DECIMAL(15,2), default 0.00
- `adjustment` - DECIMAL(15,2), default 0.00 (for credit note adjustments)
- `total` - DECIMAL(15,2), default 0.00
- `status` - VARCHAR(191), default 'Pending' (values: Pending, Approved, Completed, Cancelled)
- `created_by` - BIGINT foreign key to users, nullable
- `approved_by` - BIGINT foreign key to users, nullable
- `approved_at` - TIMESTAMP, nullable
- `created_at`, `updated_at` - TIMESTAMPS

**Foreign Key Constraints**:
- `good_receive_note_id` references good_receive_notes(id)
- `supplier_id` references suppliers(id)
- `created_by` references users(id)
- `approved_by` references users(id)

**Indexes**:
- Unique index on `return_number`
- Index on `good_receive_note_id`
- Index on `supplier_id`
- Index on `status`

#### Table 2: `supplier_return_items`

**Purpose**: Line items for supplier returns

**Fields to Create**:
- `id` - BIGINT primary key, auto-increment
- `supplier_return_id` - BIGINT foreign key, NOT NULL
- `stock_id` - BIGINT foreign key to stocks, NOT NULL
- `product_id` - BIGINT foreign key to products, NOT NULL
- `batch_id` - BIGINT foreign key to batches, NOT NULL
- `quantity_returned` - INT, NOT NULL, minimum 1
- `cost_price` - DECIMAL(10,2), NOT NULL
- `tax` - DECIMAL(5,2), default 0.00
- `item_total` - DECIMAL(10,2), NOT NULL (calculated: quantity_returned * cost_price)
- `condition` - VARCHAR(191), default 'Damaged' (values: Damaged, Defective, Wrong Item, Overstocked)
- `notes` - TEXT, nullable
- `created_at`, `updated_at` - TIMESTAMPS

**Foreign Key Constraints**:
- `supplier_return_id` references supplier_returns(id) ON DELETE CASCADE
- `stock_id` references stocks(id)
- `product_id` references products(id)
- `batch_id` references batches(id)

**Indexes**:
- Index on `supplier_return_id`
- Index on `stock_id`

### 3.4 Step 2: Create Migration Files

**Migration 1**: Create `supplier_returns` table
- File name format: `YYYY_MM_DD_HHMMSS_create_supplier_returns_table.php`
- Use Laravel Schema Builder
- Include all fields listed above with proper types and constraints
- Add foreign keys with appropriate cascade rules
- Create indexes for performance

**Migration 2**: Create `supplier_return_items` table
- File name format: `YYYY_MM_DD_HHMMSS_create_supplier_return_items_table.php`
- Ensure this migration runs AFTER supplier_returns migration
- Include CASCADE delete for supplier_return_id
- No cascade on other foreign keys (want to preserve data if stock/product deleted)

### 3.5 Step 3: Create Eloquent Models

#### Model 1: `SupplierReturn`

**Location**: `app/Models/SupplierReturn.php`

**Requirements**:
- Extend Illuminate\Database\Eloquent\Model
- Use HasFactory trait
- Define fillable array with all fields except id and timestamps
- Cast fields appropriately:
  - `return_date` → date
  - `approved_at` → datetime
  - `subtotal`, `tax`, `adjustment`, `total` → decimal:2

**Relationships to Define**:
- `goodReceiveNote()` - BelongsTo GoodReceiveNote
- `supplier()` - BelongsTo Supplier
- `items()` - HasMany SupplierReturnItem
- `createdBy()` - BelongsTo User (foreign key: created_by)
- `approvedBy()` - BelongsTo User (foreign key: approved_by)

**Query Scopes to Create**:
- `scopePending($query)` - Filter by status = 'Pending'
- `scopeApproved($query)` - Filter by status = 'Approved'
- `scopeCompleted($query)` - Filter by status = 'Completed'

#### Model 2: `SupplierReturnItem`

**Location**: `app/Models/SupplierReturnItem.php`

**Requirements**:
- Extend Model with HasFactory
- Define fillable fields
- Cast numeric fields to decimal:2

**Relationships to Define**:
- `supplierReturn()` - BelongsTo SupplierReturn
- `stock()` - BelongsTo Stock
- `product()` - BelongsTo Product
- `batch()` - BelongsTo Batch

### 3.6 Step 4: Create Service Class

#### Service: `SupplierReturnService`

**Location**: `app/Services/SupplierReturnService.php`

**Methods to Implement**:

**1. generateReturnNumber()**
- Find last SupplierReturn ordered by id DESC
- Extract numeric part from return_number (skip 'SR-' prefix)
- Increment by 1 (or start at 1 if no returns exist)
- Format as 'SR-' + zero-padded 6-digit number
- Example: SR-000001, SR-000002, etc.

**2. createSupplierReturn($returnData, $items)**
- **Wrap in database transaction**
- Create SupplierReturn record with $returnData
- Loop through each item in $items array:
  - Validate: Find stock by stock_id
  - Check: available_quantity >= quantity_returned (throw exception if not)
  - Create SupplierReturnItem with:
    - supplier_return_id (from created return)
    - stock_id, product_id, batch_id (from stock record)
    - quantity_returned (from $items)
    - cost_price (use from $items or fall back to stock->cost_price)
    - tax (from $items or stock->tax)
    - item_total (calculate: quantity_returned * cost_price)
    - condition (from $items, default 'Damaged')
    - notes (from $items, optional)
  - **Stock Deduction**: Decrement stock quantities:
    - Decrement `available_quantity` by quantity_returned
    - Decrement `quantity` by quantity_returned (permanent reduction)
- Load relationships (items, supplier, goodReceiveNote) and return

**3. approveReturn($return, $approvedBy)**
- Update SupplierReturn:
  - Set status = 'Approved'
  - Set approved_by = $approvedBy
  - Set approved_at = now()
- Return boolean success

**4. completeReturn($return)**
- Update status to 'Completed'
- Return boolean success

**5. cancelReturn($return)**
- **Wrap in database transaction**
- Loop through return->items:
  - Get stock record
  - **Restore quantities**:
    - Increment available_quantity by quantity_returned
    - Increment quantity by quantity_returned
- Update return status to 'Cancelled'
- Return boolean success

**6. getReturnableStockForGrn($grnId)**
- Query stocks where batch->good_receive_note_id = $grnId
- Filter where available_quantity > 0
- Eager load product and batch
- Return as array

**7. returnEntireBatch($batchId, $returnData)**
- Find all stocks where batch_id = $batchId AND available_quantity > 0
- Build items array with all available stock
- Call createSupplierReturn() with built data
- Return created SupplierReturn

### 3.7 Step 5: Create Controller

#### Controller: `SupplierReturnController`

**Location**: `app/Http/Controllers/SupplierReturnController.php`

**Constructor**:
- Inject SupplierReturnService via dependency injection
- Use property promotion: `protected SupplierReturnService $returnService`

**Methods to Implement**:

**1. index()**
- Query SupplierReturn with eager loading: supplier, goodReceiveNote
- Order by created_at DESC
- Paginate with 15 items per page
- Return view 'supplier-returns.index' with $returns

**2. create(Request $request)**
- Check for grn_id in query string
- If grn_id provided, load GRN with supplier
- Generate return number using service->generateReturnNumber()
- Return view 'supplier-returns.create' with returnNumber and grn

**3. getReturnableStock(GoodReceiveNote $grn)**
- Call service->getReturnableStockForGrn($grn->id)
- Return as JSON response
- **Route**: GET /good-receive-notes/{grn}/returnable-stock

**4. store(Request $request)**
- **Validate request** with these rules:
  - return_number: required, unique:supplier_returns
  - good_receive_note_id: required, exists:good_receive_notes,id
  - supplier_id: required, exists:suppliers,id
  - return_date: required, date
  - return_reason: required, string, max:191
  - notes: nullable, string
  - items: required, array, min:1
  - items.*.stock_id: required, exists:stocks,id
  - items.*.quantity_returned: required, integer, min:1
  - items.*.cost_price: required, numeric, min:0
  - items.*.tax: nullable, numeric, min:0
  - items.*.condition: required, in:Damaged,Defective,Wrong Item,Overstocked
  - items.*.notes: nullable, string

- **Calculate totals**:
  - Loop through items
  - Calculate itemTotal = quantity_returned * cost_price
  - Calculate itemTax = itemTotal * (tax / 100)
  - Sum to get subtotal and totalTax

- **Build returnData array**:
  - Include all validated fields
  - Add calculated subtotal, tax, adjustment (0), total
  - Set status = 'Pending'
  - Set created_by = auth()->id()

- **Try-Catch**:
  - Call service->createSupplierReturn($returnData, $validated['items'])
  - On success: redirect to show route with success message
  - On exception: redirect back with error message

**5. show(SupplierReturn $supplierReturn)**
- Eager load: supplier, goodReceiveNote, items.product, items.batch, items.stock, createdBy, approvedBy
- Return view 'supplier-returns.show' with $supplierReturn

**6. approve(SupplierReturn $supplierReturn)**
- Call service->approveReturn($supplierReturn, auth()->id())
- Redirect back with success message

**7. complete(SupplierReturn $supplierReturn)**
- Call service->completeReturn($supplierReturn)
- Redirect back with success message

**8. cancel(SupplierReturn $supplierReturn)**
- Call service->cancelReturn($supplierReturn)
- Redirect back with success message about stock restoration

### 3.8 Step 6: Register Routes

**File**: `routes/web.php`

**Add these routes** (wrap in auth middleware):
- GET /supplier-returns → index
- GET /supplier-returns/create → create
- POST /supplier-returns → store
- GET /supplier-returns/{supplierReturn} → show
- GET /good-receive-notes/{grn}/returnable-stock → getReturnableStock
- POST /supplier-returns/{supplierReturn}/approve → approve
- POST /supplier-returns/{supplierReturn}/complete → complete
- POST /supplier-returns/{supplierReturn}/cancel → cancel

**Route naming**: Use pattern 'supplier-returns.{action}'

### 3.9 Step 7: Create Views

**Follow existing project Tailwind patterns** from GRN views

#### View 1: `supplier-returns/index.blade.php`

**Layout**: Extend layouts.app, section 'content'

**Structure**:
- Page header with title "Supplier Returns"
- "Create Return" button (link to create route)
- Table with columns:
  - Return # (return_number)
  - GRN (goodReceiveNote->grn_number)
  - Supplier (supplier->company_name)
  - Return Date (formatted)
  - Total (formatted currency)
  - Status (color-coded badge)
  - Actions (View link)
- Pagination links at bottom

**Status Badge Colors**:
- Pending: Yellow background (bg-yellow-100 text-yellow-800)
- Approved: Blue background (bg-blue-100 text-blue-800)
- Completed: Green background (bg-green-100 text-green-800)
- Cancelled: Gray background (bg-gray-100 text-gray-800)
- Support dark mode with dark: variants

#### View 2: `supplier-returns/create.blade.php`

**Structure**:
- Form with POST to store route
- **Section 1: Return Information**
  - Display read-only return_number (hidden input)
  - Select GRN (if not pre-selected)
  - Supplier (auto-filled from GRN or selectable)
  - Return Date (date input, default today)
  - Return Reason (select dropdown)
  - Notes (textarea, optional)

- **Section 2: Items to Return**
  - If GRN selected: Fetch returnable stock via AJAX
  - Display available products with:
    - Product name
    - Available quantity
    - Cost price
  - For each selected item:
    - Quantity to return (number input, max = available_quantity)
    - Condition (select: Damaged, Defective, Wrong Item, Overstocked)
    - Notes (textarea, optional)
  - Add/Remove item buttons

- **Section 3: Totals**
  - Display calculated subtotal
  - Display tax
  - Display total
  - Calculate dynamically via JavaScript

- **Section 4: Actions**
  - Submit button
  - Cancel button (back to index)

**JavaScript Requirements**:
- Load returnable stock when GRN selected
- Calculate totals when quantities/prices change
- Validate quantity <= available before submit

#### View 3: `supplier-returns/show.blade.php`

**Structure**:
- **Header**: Return number, status badge, back button
- **Return Information Card**:
  - GRN Number (link to GRN)
  - Supplier information
  - Return Date
  - Return Reason
  - Notes
  - Created by (user name)
  - Approved by (if approved)
  - Approved at (if approved)

- **Return Items Table**:
  - Product Name (with category/brand)
  - Batch Number
  - Quantity Returned
  - Cost Price
  - Tax
  - Item Total
  - Condition
  - Notes

- **Financial Summary**:
  - Subtotal
  - Tax
  - Adjustment
  - Total

- **Actions** (conditional on status):
  - If Pending: Show "Approve" and "Cancel" buttons
  - If Approved: Show "Mark Complete" button
  - If Completed: Show completion details only

### 3.10 Business Rules to Enforce

**Validation Rules**:
1. Cannot return quantity greater than available_quantity in stock
2. Must select at least one item for return
3. Return date cannot be in the future
4. Return date cannot be before GRN received_date
5. Return number must be unique

**Stock Management Rules**:
1. Deduct from BOTH quantity and available_quantity
2. Use database transactions for atomicity
3. On cancel, restore both quantities

**Status Workflow**:
1. Create → Status = Pending
2. Pending → Approved (requires approval)
3. Approved → Completed (when physically returned)
4. Pending → Cancelled (only from pending, restores stock)
5. Cannot modify return after approval

**Financial Rules**:
1. Subtotal = Sum of (quantity_returned * cost_price) for all items
2. Tax = Sum of (item_total * tax_percentage / 100) for all items
3. Total = Subtotal + Tax + Adjustment
4. Adjustment field allows manual credit note adjustments

---

## 4. Sales Returns Implementation Plan

### 4.1 Overview

Sales returns handle customer returns of purchased products. This requires:
- Finding the original sale transaction
- Identifying which stock was sold (FIFO tracking)
- Restoring inventory (only if not damaged)
- Processing refunds or store credit

### 4.2 Prerequisites

**Important**: The sales system must be implemented first before sales returns can work.

**Assumptions for this guide**:
- A `sales` table exists with sale header information
- A `sale_items` table exists with line items from each sale
- Sales track which stock_id was sold (FIFO allocation)
- A Sale model and SaleItem model exist

### 4.3 Step 1: Create Database Tables

#### Table 1: `sales_returns`

**Purpose**: Header record for sales return transactions

**Fields to Create**:
- `id` - BIGINT primary key, auto-increment
- `return_number` - VARCHAR(191), unique, NOT NULL (format: SLR-000001)
- `sale_id` - BIGINT foreign key to sales, NOT NULL
- `customer_name` - VARCHAR(191), nullable (copy from sale or manual entry)
- `customer_phone` - VARCHAR(191), nullable
- `return_date` - DATE, NOT NULL
- `return_reason` - VARCHAR(191), NOT NULL (Changed Mind, Defective, Wrong Item, Size Issue, etc.)
- `notes` - TEXT, nullable
- `subtotal` - DECIMAL(15,2), default 0.00
- `tax` - DECIMAL(15,2), default 0.00
- `total` - DECIMAL(15,2), default 0.00
- `refund_amount` - DECIMAL(15,2), default 0.00 (may differ from total if restocking fee)
- `refund_method` - VARCHAR(191), nullable (Cash, Card, Store Credit)
- `status` - VARCHAR(191), default 'Pending' (Pending, Approved, Refunded, Cancelled)
- `processed_by` - BIGINT foreign key to users, nullable
- `processed_at` - TIMESTAMP, nullable
- `created_at`, `updated_at` - TIMESTAMPS

**Foreign Key Constraints**:
- `sale_id` references sales(id) ON DELETE CASCADE
- `processed_by` references users(id)

**Indexes**:
- Unique on return_number
- Index on sale_id
- Index on status

#### Table 2: `sales_return_items`

**Purpose**: Line items for sales returns

**Fields to Create**:
- `id` - BIGINT primary key, auto-increment
- `sales_return_id` - BIGINT foreign key, NOT NULL
- `sale_item_id` - BIGINT foreign key to sale_items, NOT NULL
- `stock_id` - BIGINT foreign key to stocks, NOT NULL (which stock to restore to)
- `product_id` - BIGINT foreign key to products, NOT NULL
- `quantity_returned` - INT, NOT NULL, minimum 1
- `selling_price` - DECIMAL(10,2), NOT NULL (original sale price)
- `tax` - DECIMAL(5,2), default 0.00
- `item_total` - DECIMAL(10,2), NOT NULL
- `condition` - VARCHAR(191), default 'Good' (Good, Damaged, Defective, Used)
- `restore_to_stock` - TINYINT, default 1 (1 = restore, 0 = don't restore)
- `notes` - TEXT, nullable
- `created_at`, `updated_at` - TIMESTAMPS

**Foreign Key Constraints**:
- `sales_return_id` references sales_returns(id) ON DELETE CASCADE
- `sale_item_id` references sale_items(id)
- `stock_id` references stocks(id)
- `product_id` references products(id)

**Indexes**:
- Index on sales_return_id
- Index on sale_item_id

### 4.4 Step 2: Create Migration Files

Create two migration files following same pattern as supplier returns:
1. `create_sales_returns_table.php`
2. `create_sales_return_items_table.php`

Use appropriate field types and constraints as specified above.

### 4.5 Step 3: Create Eloquent Models

#### Model 1: `SalesReturn`

**Location**: `app/Models/SalesReturn.php`

**Fillable Fields**: All except id and timestamps

**Casts**:
- `return_date` → date
- `processed_at` → datetime
- `subtotal`, `tax`, `total`, `refund_amount` → decimal:2

**Relationships**:
- `sale()` - BelongsTo Sale
- `items()` - HasMany SalesReturnItem
- `processedBy()` - BelongsTo User (foreign key: processed_by)

**Scopes**:
- `scopePending($query)` - Filter by status = 'Pending'
- `scopeApproved($query)` - Filter by status = 'Approved'
- `scopeRefunded($query)` - Filter by status = 'Refunded'

#### Model 2: `SalesReturnItem`

**Location**: `app/Models/SalesReturnItem.php`

**Fillable Fields**: All except id and timestamps

**Casts**:
- `selling_price`, `tax`, `item_total` → decimal:2
- `restore_to_stock` → boolean

**Relationships**:
- `salesReturn()` - BelongsTo SalesReturn
- `saleItem()` - BelongsTo SaleItem
- `stock()` - BelongsTo Stock
- `product()` - BelongsTo Product

### 4.6 Step 4: Create Service Class

#### Service: `SalesReturnService`

**Location**: `app/Services/SalesReturnService.php`

**Methods to Implement**:

**1. generateReturnNumber()**
- Same pattern as supplier returns
- Find last SalesReturn, extract number, increment
- Format: SLR-000001, SLR-000002, etc.

**2. createSalesReturn($returnData, $items)**
- **Wrap in database transaction**
- Create SalesReturn record
- Loop through items:
  - Create SalesReturnItem with all fields
  - **Stock Restoration Logic**:
    - IF restore_to_stock is true AND condition is 'Good':
      - Find stock by stock_id
      - Increment available_quantity by quantity_returned
      - Note: Do NOT increment original quantity (only available)
    - IF condition is 'Damaged' or 'Defective':
      - Set restore_to_stock = false
      - Do not restore to stock
- Load relationships and return

**3. processRefund($return, $refundMethod, $refundAmount)**
- Update SalesReturn:
  - Set status = 'Refunded'
  - Set refund_method = $refundMethod
  - Set refund_amount = $refundAmount
  - Set processed_by = auth()->id()
  - Set processed_at = now()
- Return boolean success

**4. cancelReturn($return)**
- **Wrap in database transaction**
- Loop through return->items:
  - IF restore_to_stock was true AND condition was 'Good':
    - Find stock
    - **Reverse restoration**: Decrement available_quantity
- Update status to 'Cancelled'

**5. getReturnableItemsForSale($saleId)**
- Find Sale with items and products
- Load all existing returns for this sale
- Calculate already returned quantities per sale_item_id
- For each sale item:
  - quantitySold = original quantity
  - quantityReturned = sum from all returns
  - quantityReturnable = quantitySold - quantityReturned
  - Only include if quantityReturnable > 0
- Return array with returnable items data

### 4.7 Step 5: Create Controller

Similar structure to SupplierReturnController but adapted for sales.

**Methods**: index, create, store, show, processRefund, cancel

**Additional method**: `getReturnableItems(Sale $sale)` - Returns JSON of returnable items

### 4.8 Step 6: Register Routes

Add routes similar to supplier returns, using 'sales-returns' prefix.

### 4.9 Step 7: Create Views

Create views following same pattern as supplier returns:
- index.blade.php - List all sales returns
- create.blade.php - Create new sales return
- show.blade.php - View sales return details

**Key Differences**:
- Select Sale instead of GRN
- Show customer information instead of supplier
- Include refund method and amount fields
- Add "Restore to Stock" checkbox per item
- Show condition selector (Good, Damaged, Defective, Used)

### 4.10 Business Rules Specific to Sales Returns

**Validation Rules**:
1. Cannot return more than originally purchased
2. Must account for previous partial returns
3. Return date cannot be before sale date
4. If condition is not 'Good', default restore_to_stock to false

**Stock Restoration Rules**:
1. Only restore to available_quantity, never to quantity field
2. Only restore if condition is 'Good' AND restore_to_stock is true
3. Restore to the SAME stock_id that was sold (maintain FIFO integrity)

**Damaged Goods Handling**:
1. Track damaged returns separately
2. Don't add back to sellable inventory
3. Can be used for supplier claims if under warranty

**Refund Rules**:
1. Refund amount can be less than total (restocking fees)
2. Support Cash, Card, and Store Credit methods
3. Track who processed the refund and when

**Partial Returns**:
1. Allow returning only some items from a sale
2. Allow returning partial quantities of an item
3. Track cumulative returned quantities per sale_item

---

## 5. Testing Requirements

### 5.1 What to Test for Supplier Returns

**Unit Tests** (Service Layer):
1. Test return number generation increments correctly
2. Test return number starts at SR-000001 when no returns exist
3. Test creating return with valid data succeeds
4. Test creating return deducts from stock quantities
5. Test exception thrown when returning more than available
6. Test approval updates status and timestamps correctly
7. Test cancellation restores stock quantities
8. Test getting returnable stock filters correctly

**Feature Tests** (Controller):
1. Test authenticated user can view supplier returns index
2. Test authenticated user can create supplier return
3. Test validation fails with invalid data (no items, invalid quantities, etc.)
4. Test return creation redirects with success message
5. Test cannot create return without authentication
6. Test approval requires proper permissions
7. Test stock is actually deducted after return creation

**Edge Cases to Test**:
1. Returning from a GRN with no available stock
2. Returning the exact available quantity
3. Multiple returns from same GRN
4. Cancelling a return correctly restores both quantity fields
5. Cannot approve already completed return

### 5.2 What to Test for Sales Returns

**Unit Tests**:
1. Test return number generation (SLR-000001 format)
2. Test creating return restores stock only when condition is Good
3. Test creating return with Damaged condition doesn't restore stock
4. Test refund processing updates all fields correctly
5. Test returnable items calculation accounts for previous returns
6. Test partial return quantities calculate correctly

**Feature Tests**:
1. Test customer can initiate return (if allowing self-service)
2. Test staff can process return with refund
3. Test validation prevents returning more than purchased
4. Test validation prevents returning already fully returned items
5. Test refund amount can differ from total

**Edge Cases**:
1. Returning all items from a sale
2. Multiple partial returns from same sale
3. Returning item that was sold from multiple stock entries (FIFO)
4. Restocking fee scenarios (refund < total)
5. Store credit vs cash refund workflows

---

## 6. UI/UX Guidelines

### 6.1 Design Principles

1. **Consistency**: Follow existing GRN view patterns exactly
2. **Dark Mode**: All elements must work in both light and dark modes
3. **Responsive**: Tables must be responsive with horizontal scroll on mobile
4. **Clear Status**: Use color-coded badges for all statuses
5. **Accessibility**: Proper labels, ARIA attributes, keyboard navigation

### 6.2 Color Coding Standards

**Status Colors** (use for both supplier and sales returns):
- **Pending**: bg-yellow-100 text-yellow-800 (dark: bg-yellow-900 text-yellow-200)
- **Approved**: bg-blue-100 text-blue-800 (dark: bg-blue-900 text-blue-200)
- **Completed**: bg-green-100 text-green-800 (dark: bg-green-900 text-green-200)
- **Refunded**: bg-green-100 text-green-800 (dark: bg-green-900 text-green-200)
- **Cancelled**: bg-gray-100 text-gray-800 (dark: bg-gray-700 text-gray-300)

**Condition Colors** (for item conditions):
- **Good**: Green
- **Damaged**: Red
- **Defective**: Orange
- **Wrong Item**: Yellow

### 6.3 Form Layout Standards

Follow GRN create form structure:

**Section 1: Header Information**
- Display generated return number (read-only)
- Selection dropdowns (GRN/Sale)
- Related information (Supplier/Customer)
- Date picker
- Reason dropdown
- Notes textarea

**Section 2: Items Selection**
- Button to load available items
- Table or cards showing available items
- For each item:
  - Product name, SKU, available quantity
  - Quantity input (with max validation)
  - Condition selector
  - Item notes
  - Remove button
- Add item button

**Section 3: Summary**
- Subtotal (auto-calculated)
- Tax (auto-calculated)
- Adjustment field (manual, optional)
- Total (auto-calculated)

**Section 4: Actions**
- Primary action button (Create Return, Process Refund, etc.)
- Secondary action (Cancel, Back)

### 6.4 Table Design Standards

**Index Tables** must include:
- Sortable columns (Return #, Date, Amount)
- Filterable status badges
- Pagination controls
- Actions column (View, Edit if applicable)
- Responsive design with overflow-x-auto

**Detail Tables** (items) must include:
- Product information
- Quantities (with units)
- Prices (formatted as currency)
- Status/Condition badges
- Notes preview (with expand option)

### 6.5 JavaScript Functionality Requirements

**Dynamic Calculations**:
- Update totals when quantities or prices change
- Recalculate when items added or removed
- Display running totals clearly

**AJAX Operations**:
- Load returnable items without page refresh
- Show loading states during requests
- Handle errors gracefully with user feedback

**Form Validation**:
- Client-side validation before submit
- Max quantity validation (can't exceed available)
- Required field indicators
- Inline error messages

**User Feedback**:
- Success messages (toast or flash)
- Error messages with specifics
- Confirmation dialogs for destructive actions
- Loading spinners for async operations

---

## 7. Implementation Summary

### 7.1 Supplier Returns Checklist

- [ ] Create `supplier_returns` migration
- [ ] Create `supplier_return_items` migration
- [ ] Run migrations
- [ ] Create `SupplierReturn` model with relationships
- [ ] Create `SupplierReturnItem` model with relationships
- [ ] Create `SupplierReturnService` with all methods
- [ ] Create `SupplierReturnController` with all actions
- [ ] Register all routes in web.php
- [ ] Create index.blade.php view
- [ ] Create create.blade.php view with JavaScript
- [ ] Create show.blade.php view
- [ ] Write unit tests for service
- [ ] Write feature tests for controller
- [ ] Test all workflows (create, approve, complete, cancel)

### 7.2 Sales Returns Checklist

- [ ] Ensure Sales system is implemented first
- [ ] Create `sales_returns` migration
- [ ] Create `sales_return_items` migration
- [ ] Run migrations
- [ ] Create `SalesReturn` model with relationships
- [ ] Create `SalesReturnItem` model with relationships
- [ ] Create `SalesReturnService` with all methods
- [ ] Create `SalesReturnController` with all actions
- [ ] Register routes
- [ ] Create index view
- [ ] Create create view with refund options
- [ ] Create show view
- [ ] Write unit tests
- [ ] Write feature tests
- [ ] Test stock restoration logic
- [ ] Test refund processing

### 7.3 Critical Implementation Notes

**Always Remember**:
1. Use database transactions when modifying stock quantities
2. Validate quantities before deducting/restoring stock
3. Cast all numeric fields to appropriate types (decimal, integer)
4. Follow existing project naming conventions
5. Add proper indexes for performance
6. Test both happy path and edge cases
7. Document any deviations from this plan

**Stock Management**:
- Supplier returns: Decrement both `quantity` and `available_quantity`
- Sales returns: Increment only `available_quantity` (preserve original record)
- Always wrap stock modifications in transactions
- Always validate before modifying

**Status Workflows**:
- Don't allow status changes in wrong direction
- Log who performed status changes (created_by, approved_by, processed_by)
- Timestamp all status changes

---

**End of Implementation Guide**

This document provides the complete roadmap for implementing both return systems. Follow each step in order, test thoroughly, and maintain data integrity throughout.
