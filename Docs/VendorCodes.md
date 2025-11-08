Vendor Code Implementation Plan
Overview
Transform the GRN system to work with vendor-specific product codes instead of internal SKUs. Each supplier can have their own codes for products, enabling GRN creation using vendor codes.
Database Changes
1. Create product_supplier Pivot Table
- id (primary key)
- product_id (FK → products)
- supplier_id (FK → suppliers)
- vendor_product_code (string, indexed) // Supplier's SKU for this product
- vendor_barcode (nullable string) // Supplier's barcode
- vendor_cost_price (nullable decimal) // Supplier's default cost
- is_preferred (boolean, default false) // Preferred supplier for product
- lead_time_days (nullable integer) // Delivery lead time
- minimum_order_quantity (nullable integer)
- timestamps
- Unique constraint on (supplier_id, vendor_product_code)
2. Fix Batches Table Barcode Column
Add missing barcode column that's currently in the model but not in migration.
Model Updates
Product Model
Add belongsToMany(Supplier) relationship with pivot fields
Add suppliers() method with withPivot for vendor_product_code, etc.
Supplier Model
Add belongsToMany(Product) relationship with pivot fields
Add products() method with withPivot
GRN Workflow Changes
1. GRN Create Form
Supplier Selection Impact:
When supplier is selected, dynamically filter products to show only those linked to that supplier
Display vendor codes alongside product names: "Product Name (Vendor Code: ABC-123, Internal SKU: PROD-001)"
Add search by vendor code functionality
Show vendor cost price as default (can be overridden)
2. Product Selection Dropdown
// After supplier selection, AJAX call to fetch supplier's products
GET /api/suppliers/{id}/products
// Returns products with pivot data (vendor_product_code, vendor_cost_price)
3. Controller Updates
GoodReceiveNoteController@create: Pass empty products array initially
Add new route: GET /api/suppliers/{id}/products to return supplier's products with vendor codes
GoodReceiveNoteController@store: Validation remains similar, but leverage vendor codes
4. Service Layer Updates
GoodReceiveNoteService: No major changes needed, batch generation continues using internal product SKU
New Features
1. Product-Supplier Management
Create a dedicated interface to manage product-supplier relationships:
Assign suppliers to products
Set vendor codes, barcodes, cost prices
Mark preferred suppliers
Bulk import vendor catalog
2. Vendor Code Search
Add quick search by vendor code in GRN form:
Type vendor code → auto-select correct product
Display product info + vendor details
Implementation Steps
Database Migration - Create product_supplier table and add barcode to batches
Fix Barcode - Add barcode column to batches migration
Model Relationships - Update Product and Supplier models
API Endpoint - Create supplier products endpoint
Update GRN Form - Add dynamic product loading based on supplier
Update Controllers - Handle new workflow
Product-Supplier UI - Create management interface (optional for MVP)
Testing - Test GRN creation with vendor codes
Migration Path
For Existing Data:
Products without supplier relationships will still be accessible in GRN
Add "All Products" option in supplier dropdown to show unlinked products
Gradual migration: Link products to suppliers over time
Benefits
✅ Each supplier can have their own product codes
✅ Easier GRN creation using vendor invoices/packing slips
✅ Track supplier-specific pricing
✅ Support multiple suppliers for same product
✅ Supplier catalog management
✅ Better inventory accuracy