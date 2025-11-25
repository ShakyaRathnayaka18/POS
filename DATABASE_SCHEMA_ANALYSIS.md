# Database Schema vs Migrations Comparison Report

## Executive Summary
The database contains **361 tables** with a critical issue: **Most tables have NO column details recorded**. Only 18 tables have actual column information in the schema output. This indicates either:
1. The database schema introspection tool has limitations
2. Many tables are empty/unused legacy tables
3. Permission issues preventing full schema inspection

---

## KEY FINDINGS

### 1. Tables WITH Column Details (27 tables - ACTIVE/IMPORTANT)

These are tables that have their column structure fully defined in the database:

#### Core Laravel System (10 tables):
- **users** - 11 columns (id, name, username, email, password, 2FA fields, tokens, timestamps)
- **migrations** - 3 columns (id, migration, batch)
- **jobs** - 7 columns (id, queue, payload, attempts, reserved_at, available_at, created_at)
- **cache** - 3 columns (key, value, expiration)
- **cache_locks** - 3 columns (key, owner, expiration)
- **sessions** - 6 columns (id, user_id, ip_address, user_agent, payload, last_activity)
- **password_reset_tokens** - 3 columns (email, token, created_at)
- **failed_jobs** - 7 columns (id, uuid, connection, queue, payload, exception, failed_at)
- **job_batches** - 10 columns (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at)

#### Authentication & Authorization (5 tables):
- **roles** - 5 columns (id, name, guard_name, timestamps)
- **permissions** - 5 columns (id, name, guard_name, timestamps)
- **model_has_roles** - 3 columns (role_id, model_type, model_id) + 2 indexes
- **model_has_permissions** - 3 columns (permission_id, model_type, model_id) + 2 indexes
- **role_has_permissions** - 2 columns (permission_id, role_id) + 2 indexes

#### Business Core (2 tables):
- **categories** - 6 columns (id, cat_name, description, icon, timestamps) + unique on cat_name
- **brands** - 6 columns (id, brand_name, description, logo, timestamps) + unique on brand_name

#### Accounting Module (8 tables):
- **account_types** - 5 columns (id, name, normal_balance:enum, category, timestamps) + unique on name
- **accounts** - 8 columns (id, account_code:unique, account_name, account_type_id:FK, parent_account_id:FK, description, is_active, timestamps)
- **account_balances** - 11 columns (id, account_id, fiscal_year, fiscal_period, opening_balance, debit_total, credit_total, closing_balance, timestamps) + unique constraint + 2 indexes
- **journal_entries** - 15 columns (id, entry_number:unique, entry_date, fiscal_year, fiscal_period, description, reference_type, reference_id, status:enum, created_by:FK, approved_by:FK, posted_at, voided_by:FK, voided_at, timestamps) + 2 indexes
- **fiscal_periods** - 11 columns (id, name, year, month, start_date, end_date, status:enum, closed_by:FK, closed_at, timestamps) + unique(year,month) + status_index
- **expense_categories** - 5 columns (id, category_name:unique, description, is_active, timestamps)
- **expenses** - 20 columns (id, expense_number:unique, expense_category_id:FK, title, description, amount, expense_date, payment_method, reference_number, receipt_path, status, approved_by:FK, approved_at, paid_by:FK, paid_at, notes, created_by:FK, timestamps, soft_delete)
- **journal_entry_lines** - 8 columns (id, journal_entry_id:FK, account_id:FK, debit_amount, credit_amount, description, line_number, timestamps) + 2 indexes

#### Employee & Payroll (1 table):
- **employees** - 22 columns (id, user_id, employee_number:unique, hire_date, termination_date, employment_type:enum, hourly_rate, base_salary, pay_frequency:enum, department, position, epf_number, bank_name, bank_account_number, bank_account_name, status:enum, notes, timestamps, soft_delete) + 4 indexes

#### Inventory (1 table):
- **batches** - 8 columns (id, batch_number:unique, barcode:unique, good_receive_note_id:FK, manufacture_date, expiry_date, notes, timestamps)

---

### 2. Tables WITHOUT Column Details (334 tables - LEGACY/UNUSED)

These tables have **empty column arrays**, indicating they are either:
- Unused legacy tables from previous application versions
- Temporary/working tables
- Corrupted or inaccessible

**High-level categorization:**

#### Legacy Accounting Tables (13 tables):
account_basement, account_heading, account_numbers, account_posting, account_runing (typo), account_runing_bank_statetment, account_runing_copy1, account_runing_copy2, account_runing_copy_dil, account_runing_del, account_running_reversals, account_titel, account_posting_copy1

#### Travel/Tourism Legacy (39 tables):
booking_option, booking_tabs, bookings, itinerary, passenger_details, passengers, pax_infant, pass_wise_inv, payment_history, payment_request, pnr_content, reservation, temp_passengers, tour_activity, tour_itinerary, tour_quotations, tour_quotations_copy1, tour_sub_locations, international_airport, hotel_rate, room_type, service_packages, inquiry*, quotation*, etc.

#### Shipping/Logistics Legacy (28 tables):
agent_location, box_sizes, cob, conformed_orders, container, customer_packages, extra_fees, load_container, order_log, order_reference, package_extra_fee, pickup_request, shipping_containers, shipping_date, transfer_milage, etc.

#### Stock/Inventory Legacy (32 tables):
stock, stock_book, stock_card, stock_cat, stock_cost, stock_orders, stock_in, stock_orders_in, stock_sup, stock_adjustment, stock_exchange, stocktransfer_temp, supplier_refunds (new), semi_product, spare_parts, generic_names, etc.

#### Old E-commerce (45+ tables):
books, books_copy1, cart_details, deleted_books, order_details, order_details_temp, order_fail, order_status, order_verify, order_verify_log, product_reviews, wish_list, wholesale_cust, visitors, gallery, home_banners, home_carousels, promo_banner, etc.

#### Chat/Communication/Tasks (20+ tables):
chats, conversations, chat_attachments, project_channels, project_messages, projects, tasks, task_assignees, task_attachments, task_comments, task_completion_logs, task_notes, task_status_histories, time_logs, daily_tasks, boards, qa_test_rounds, qa_assignments, qa_test_attachments

#### Other Business Logic (180+ tables):
drivers, vehicles, routes, drivers_advance_request, repairs, repair_faults, repair_service, repair_job_invoice, repair_invoice_details, repair_transfer_log, machines, bom*, grn_tkn*, menu_*, user_acc_tbl, downloads, emails, sms_log, whatsapp_log, and many others...

---

### 3. Critical Discrepancies

#### A. Migrations Exist → Tables NOT Showing with Columns (14 tables)

These tables are created by migrations but **missing column details** in schema output:

1. **good_receive_notes** - 2 migrations: update_good_receive_notes_columns, add_invoice_fields_to_good_receive_notes_table
2. **purchase_orders** - Migration: 2025_07_10_093810_create_purchase_orders_table.php
3. **products** - Multiple migrations (create, remove_price_and_tax, add_item_code, remove_barcode)
4. **suppliers** - Migration: 2025_07_10_092813_create_suppliers_table.php
5. **supplier_credits** - Migration: 2025_11_13_172737_create_supplier_credits_table.php
6. **supplier_payments** - Migration: 2025_11_13_172746_create_supplier_payments_table.php
7. **payment_reminders** - Migration: 2025_11_13_172757_create_payment_reminders_table.php
8. **supplier_returns** - Migration: 2025_11_08_120000_create_supplier_returns_table.php
9. **supplier_return_items** - Migration: 2025_11_08_120001_create_supplier_return_items_table.php
10. **sales** - Migration: 2025_11_08_130000_create_sales_tables.php
11. **sales_returns** - Migration: 2025_11_08_130001_create_sales_returns_table.php
12. **sales_return_items** - Migration: 2025_11_08_130002_create_sales_return_items_table.php
13. **saved_carts** - Migration: 2025_11_10_154834_create_saved_carts_table.php
14. **saved_cart_items** - Migration: 2025_11_10_154843_create_saved_cart_items_table.php
15. **shifts** - Migration: 2025_11_13_103626_create_shifts_table.php
16. **payroll_periods** - Migration: 2025_11_13_155048_create_payroll_periods_table.php
17. **payroll_entries** - Migration: 2025_11_13_155048_create_payroll_entries_table.php
18. **payroll_entry_shift** - Migration: 2025_11_13_155141_create_payroll_entry_shift_table.php
19. **payroll_settings** - Migration: 2025_11_16_143338_create_payroll_settings_table.php
20. **product_supplier** - Migration: 2025_11_08_101845_create_product_supplier_table.php
21. **stocks** - Migration: 2025_11_05_105236_create_stocks_table.php

**Issue:** Schema introspection may be truncated or these tables have visibility issues.

#### B. Database Tables WITHOUT Migrations (300+ tables)

All legacy tables exist in the database but have NO migration files in `database/migrations/`. Examples:
- All the typo'd "account_runing" tables
- All travel/tourism tables (bookings, passengers, inquiries, etc.)
- All shipping tables from old system
- All e-commerce tables (books, orders, cart_details, etc.)
- All chat/project/task tables
- All hotel/restaurant tables

**Implication:** These are from a previous application and should be archived/removed.

#### C. Foreign Key Constraints NOT Shown in Schema

**Migration definitions include FKs but schema shows none:**

From migration files read:
```php
// accounts migration defines:
$table->foreignId('account_type_id')->constrained('account_types')->cascadeOnDelete();
$table->foreignId('parent_account_id')->nullable()->constrained('accounts')->nullOnDelete();

// journal_entries migration defines:
$table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
$table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
$table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();

// journal_entry_lines migration defines:
$table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
$table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
```

**But schema output shows:** `"foreign_keys": []` for all tables

**Possible causes:**
1. MySQL `foreign_key_checks` is OFF
2. Foreign keys not enforced in development
3. Schema introspection tool limitation
4. Constraints not created during migration

---

## Migrations Audit (47 total)

### Core Laravel (3):
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000001_create_cache_table.php
- 0001_01_01_000002_create_jobs_table.php

### Authentication & Permissions (1):
- 2025_11_12_154344_create_permission_tables.php

### User Management (1):
- 2025_11_13_084040_add_two_factor_columns_to_users_table.php
- 2025_11_13_153636_create_employees_table.php

### Payroll System (5):
- 2025_11_13_155048_create_payroll_entries_table.php
- 2025_11_13_155048_create_payroll_periods_table.php
- 2025_11_13_155141_create_payroll_entry_shift_table.php
- 2025_11_16_143338_create_payroll_settings_table.php
- 2025_11_16_145001_add_ot_fixed_rate_fields_to_payroll_settings_table.php

### Shift Management (2):
- 2025_11_13_103626_create_shifts_table.php
- 2025_11_13_103658_add_shift_id_to_sales_table.php

### Inventory/Products (8):
- 2025_06_24_041324_create_categories_table.php
- 2025_06_24_041332_create_brands_table.php
- 2025_06_24_041337_create_products_table.php
- 2025_11_05_105142_remove_price_and_tax_from_products_table.php
- 2025_11_07_075648_add_item_code_to_products_table.php
- 2025_11_07_075735_remove_barcode_from_products_table.php
- 2025_11_07_075812_add_barcode_to_batches_table.php
- 2025_11_08_101845_create_product_supplier_table.php

### Suppliers/Purchasing (7):
- 2025_07_10_092813_create_suppliers_table.php
- 2025_07_10_093810_create_purchase_orders_table.php
- 2025_11_13_172737_create_supplier_credits_table.php
- 2025_11_13_172746_create_supplier_payments_table.php
- 2025_11_13_172757_create_payment_reminders_table.php
- 2025_11_13_172805_add_credit_fields_to_good_receive_notes_table.php
- 2025_11_13_172813_add_credit_tracking_to_suppliers_table.php

### Receiving/GRN (3):
- 2025_11_05_105229_create_batches_table.php
- 2025_11_05_105236_create_stocks_table.php
- 2025_11_07_073435_update_good_receive_notes_columns.php
- 2025_11_14_080157_add_invoice_fields_to_good_receive_notes_table.php

### Supplier Returns (2):
- 2025_11_08_120000_create_supplier_returns_table.php
- 2025_11_08_120001_create_supplier_return_items_table.php

### Sales (6):
- 2025_11_08_130000_create_sales_tables.php
- 2025_11_08_130001_create_sales_returns_table.php
- 2025_11_08_130002_create_sales_return_items_table.php
- 2025_11_09_031052_add_fields_to_sales_table.php
- 2025_11_09_200000_add_customer_details_to_sales_table.php
- 2025_11_10_154834_create_saved_carts_table.php
- 2025_11_10_154843_create_saved_cart_items_table.php

### Accounting/Finance (8):
- 2025_11_14_090157_create_expense_categories_table.php
- 2025_11_14_090343_create_expenses_table.php
- 2025_11_14_102748_create_account_types_table.php
- 2025_11_14_102752_create_accounts_table.php
- 2025_11_14_102754_create_journal_entries_table.php
- 2025_11_14_102757_create_journal_entry_lines_table.php
- 2025_11_14_102759_create_fiscal_periods_table.php
- 2025_11_14_102801_create_account_balances_table.php

---

## Summary Table

| Category | Active (with columns) | Legacy (empty) | Status |
|----------|----------------------|----------------|--------|
| Core Laravel | 9 | 0 | ACTIVE |
| Auth/Roles/Permissions | 5 | 0 | ACTIVE |
| Accounting | 8 | 13 | MIXED |
| Employees/Payroll | 1 | 0 | INCOMPLETE* |
| Inventory | 1 | 32 | MIXED |
| Sales | 0 | 5+ | INCOMPLETE* |
| Suppliers/Purchasing | 0 | 6+ | INCOMPLETE* |
| E-commerce (old) | 0 | 45+ | LEGACY |
| Travel/Tourism | 0 | 39+ | LEGACY |
| Chat/Tasks/Projects | 0 | 20+ | LEGACY |
| Shipping/Logistics | 0 | 28+ | LEGACY |
| **TOTAL** | **27** | **334+** | **361 total** |

*Incomplete = migrations exist but columns not shown in schema output

---

## Recommended Actions for Consolidation

### Phase 1: Investigation & Backup (CRITICAL)
- [ ] Run raw SQL: `SELECT TABLE_NAME, COLUMN_COUNT(*) FROM information_schema.TABLES`
- [ ] Verify all 47 migrated tables actually exist and have correct columns
- [ ] Check if foreign_key_checks is enabled: `SELECT @@foreign_key_checks`
- [ ] Export schemas of 300+ legacy tables before deletion (backup)
- [ ] Identify which legacy tables contain data (if any)

### Phase 2: Verification
- [ ] Confirm no application code references legacy tables
- [ ] Search codebase for table names: `account_runing`, `booking*`, `pickup_request`, etc.
- [ ] Check Laravel models in `app/Models/`
- [ ] Check database queries in services/controllers
- [ ] Verify cached queries don't reference old tables

### Phase 3: Cleanup Strategy
**Option A - Safe Approach:**
1. Export/backup all 300+ legacy tables
2. Drop legacy tables in batches with verification between drops
3. Fix any missing foreign keys in migrated tables
4. Add any missing indexes from migrations

**Option B - Aggressive Approach:**
1. Create new clean database from migrations only
2. Verify all application functionality
3. Migrate only necessary data from old database
4. Swap to new database

### Phase 4: Consolidation
- [ ] Drop legacy tables (in backup)
- [ ] Verify all 47 migrated tables with correct schema
- [ ] Verify all foreign keys are created
- [ ] Run complete test suite
- [ ] Monitor application for errors

### Phase 5: Documentation
- [ ] Document which tables were removed and why
- [ ] Create migration reconciliation report
- [ ] Update database documentation
- [ ] Record any data preserved from legacy tables

---

## Critical Questions for Implementation

1. **Are the 21 migrated tables showing no columns actually missing from the database?**
   - Need to verify with raw SQL

2. **Do foreign keys actually exist in the database?**
   - Check `INFORMATION_SCHEMA.KEY_COLUMN_USAGE` directly

3. **Is there any application code using the 300+ legacy tables?**
   - Need full codebase grep/search

4. **What is the purpose of the legacy tables?**
   - Are they archives? Old versions? Development tables?
   - Should they be preserved or discarded?

5. **Is production database identical to this development database?**
   - Production consolidation strategy may differ

---

## Schema Validation Checklist

After consolidation, verify:
- [ ] All 47 migrations run without errors
- [ ] All 27 active tables with columns match migration definitions
- [ ] All foreign keys are enforced
- [ ] All indexes are created
- [ ] No orphaned tables remain
- [ ] Database is <5% of original size (approximate)
- [ ] All application functionality works
- [ ] No broken relationships or constraints
