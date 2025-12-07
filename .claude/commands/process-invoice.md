# Process Invoice Command

Process an invoice file (PDF, text, or image) and generate SQL INSERT statements for products with vendor codes.

## Steps to Execute:

### 1. Read and Extract Invoice Data
- Read the invoice file from the provided path
- Extract the following information:
  - Supplier/Distributor name from invoice header
  - List of products with their details
  - Vendor codes (if present in format like "CODE123 - Product Name")

### 2. Query Live Database for IDs
Query the live MySQL database to determine:

```sql
-- Get all categories
SELECT id, cat_name FROM categories ORDER BY cat_name;

-- Get all brands
SELECT id, brand_name FROM brands ORDER BY brand_name;

-- Get all suppliers
SELECT id, company_name FROM suppliers ORDER BY company_name;

-- Get current max SKU
SELECT MAX(id) as max_id, MAX(sku) as max_sku FROM products;
```

### 3. Clean Product Names
Remove quantity information from product names:
- Remove patterns like: `*10`, `*12`, `(1x20)`, `(36X10)`, etc.
- Keep size/weight information: `200G`, `250ML`, `4.6W`, etc.
- Extract vendor codes if in format: `VENDORCODE - Product Name`

### 4. Determine Category, Brand, and Supplier
Based on the invoice content and supplier name:
- Match supplier name with database suppliers
- Identify product category (Personal Care, Electric Items, Snacks, etc.)
- Identify brand(s) - may be multiple brands in one invoice

### 5. Generate SQL INSERT Statements
Create two SQL statements in `invoice.txt`:

**Statement 1: Insert Products**
```sql
INSERT INTO products (product_name, sku, category_id, brand_id, created_at, updated_at) VALUES
('Product Name 1', 'SKU-XXXXXX', category_id, brand_id, NOW(), NOW()),
('Product Name 2', 'SKU-XXXXXX', category_id, brand_id, NOW(), NOW()),
...;
```

**Statement 2: Link to Supplier with Vendor Codes**
```sql
INSERT INTO product_supplier (product_id, supplier_id, vendor_product_code, is_preferred, lead_time_days, created_at, updated_at) VALUES
((SELECT id FROM products WHERE sku = 'SKU-XXXXXX'), supplier_id, 'VENDOR-CODE', 1, NULL, NOW(), NOW()),
...;
```

**Vendor Code Format:**
- If vendor codes exist in invoice: Use them directly
- If no vendor codes: Generate as `{SUPPLIER_PREFIX}-{SKU_NUMBER}`
  - Example: `SIN-000310` for Singhe Agencies
  - Example: `LAS-000349` for Lasantha Distributor

### 6. Ask User for Permission
Display summary:
```
Found X products from [Supplier Name]
- Category: [Category Name] (ID: X)
- Brand(s): [Brand Name(s)] (ID: X)
- Supplier: [Supplier Name] (ID: X)
- SKUs: SKU-XXXXXX to SKU-XXXXXX

SQL has been written to invoice.txt

Do you want to insert these products into the live database? (yes/no)
```

### 7. Execute SQL on Live Database (if approved)
Run the INSERT statements using the live MySQL MCP server:
```
mcp__mysql-live__execute_query
```

Confirm success and show row counts.

## Example Usage:
```
/process-invoice E:\Herd\POS\Invoices\Invoice_2.pdf
```

## Important Notes:
- Always check current max SKU before generating new SKUs
- Handle multiple brands in single invoice
- Vendor codes must be unique per supplier (enforced by database constraint)
- Ask permission before executing any INSERT statements
