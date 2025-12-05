# SaaS Transformation Implementation Plan

## 1. Executive Summary
Transform the existing POS system into a robust, scalable Multi-Tenant SaaS platform.
**Goal**: Allow vendors to register, subscribe via Webxpay, and instantly access a private, secure POS instance.
**Key Tech**: Laravel 11+, `stancl/tenancy` (Database-per-tenant), Webxpay.

## 2. Architectural Blueprint

### 2.1 Multi-Tenancy Strategy: Database-per-Tenant
*   **Why**: Maximum security and data isolation. Each vendor gets their own database (`tenant_db_uuid`).
*   **Package**: `stancl/tenancy` v3+.
*   **Tenant Identification**: Subdomains (e.g., `vendor.pos-app.com`) or Custom Domains.

### 2.2 Application Architecture (Laravel Best Practices)
To ensure scalability and maintainability as a SaaS, we will adopt a **Modular Monolith** approach with a strict **Service Layer**.

*   **Service Layer**: All business logic (e.g., `CreateSale`, `ProcessPayroll`) moves from Controllers to Service Classes.
    *   *Benefit*: Reusable, testable, and ensures tenant scoping is applied consistently.
*   **DTOs (Data Transfer Objects)**: Use `spatie/laravel-data` or simple PHP classes to pass data between requests and services.
*   **Queues**: Heavy operations (Tenant creation, Bulk imports, Emails) MUST be queued using Redis/Horizon.

### 2.3 Payment Flow (Webxpay)
Since Webxpay lacks a native Laravel Cashier driver, we will build a custom integration.

1.  **Plan Selection**: User chooses a plan (Monthly/Yearly).
2.  **Payment Initiation**: App generates a secure hash and redirects to Webxpay.
3.  **Verification**:
    *   **Webhook/Callback**: Webxpay notifies `https://central.app/api/webxpay/callback`.
    *   **Verification Job**: A queued job verifies the signature and status.
4.  **Provisioning**: On success, the `CreateTenant` job triggers.

## 3. Detailed Implementation Steps

### Phase 1: Infrastructure & Core Setup
- [ ] **Install Tenancy**: `composer require stancl/tenancy`.
- [ ] **Config**: Publish assets and configure `config/tenancy.php`.
    -   Set `central_domains` in `.env`.
    -   Configure `tenancy` database connection (mysql).
- [ ] **Migration Restructuring**:
    -   **Central**: `users` (landlord admins), `tenants`, `domains`, `subscriptions`, `activity_logs`.
    -   **Tenant**: Move ALL POS-related tables (`products`, `sales`, `employees`, etc.) to `database/migrations/tenant`.

### Phase 2: Central Application (The "Storefront")
- [ ] **Landing Page**: High-conversion homepage explaining features.
- [ ] **Registration Wizard**:
    -   Step 1: User Details (Name, Email).
    -   Step 2: Store Details (Store Name -> generates `store-name` subdomain).
    -   Step 3: Plan Selection & Payment.

### Phase 3: Webxpay Integration Module
- [ ] **Service**: `App\Services\Payment\WebxpayService`.
- [ ] **Controller**: `WebxpayController` (handles redirect and callback).
- [ ] **Model**: `Subscription` (tracks status, renewal date, transaction refs).
- [ ] **Command**: `php artisan subscription:check` (Daily cron to check expiring subs).

### Phase 4: Tenant Onboarding & Logic
- [ ] **Tenant Provisioning Job**:
    ```php
    // Pseudo-code
    $tenant = Tenant::create(['id' => $storeName]);
    $tenant->domains()->create(['domain' => $storeName . '.' . config('app.domain')]);
    User::create([...]); // Create admin inside tenant DB
    ```
- [ ] **Middleware**:
    -   `PreventAccessFromCentralDomains`: Protects POS routes.
    -   `CheckSubscription`: Redirects to billing if expired.

### Phase 5: UI/UX & "Wow" Factors
- [ ] **Dashboard**: Create a "Central Dashboard" for tenants to view their subscription and invoices.
- [ ] **Theme Customization**: Allow tenants to upload their logo (stored in `storage/tenant/{id}/app/public`).

## 4. Recommended Packages
*   `stancl/tenancy`: The core engine.
*   `spatie/laravel-permission`: (Already installed) - Ensure it's configured for tenant-specific roles.
*   `laravel/horizon`: For managing queues (critical for tenant provisioning).
*   `sentry/sentry-laravel`: For error tracking (tenant-aware).

## 5. Security Checklist
- [ ] **Rate Limiting**: Apply strict limits on API routes per tenant IP.
- [ ] **HTTPS**: Use Laravel Forge or Caddy for automatic wildcard SSL.
- [ ] **Backups**: Configure `spatie/laravel-backup` to back up all tenant databases individually.

## 6. Execution Plan
1.  **Branch**: `git checkout -b feature/saas-core`
2.  **Migrate**: Move migrations and test `tenancy:migrate`.
3.  **Routes**: Split `web.php` into `web.php` (Central) and `tenant.php` (POS).
4.  **Develop**: Build the Registration -> Payment -> Provisioning flow.
5.  **Verify**: Test full cycle with a sandbox Webxpay account.

---
*Refined by Antigravity with insights from Laravel architectural best practices.*
