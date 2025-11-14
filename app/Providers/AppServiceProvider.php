<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\GoodReceiveNote;
use App\Models\Sale;
use App\Models\SupplierPayment;
use App\Observers\ExpenseObserver;
use App\Observers\GoodReceiveNoteObserver;
use App\Observers\SaleObserver;
use App\Observers\SupplierPaymentObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register observers for accounting integration
        Sale::observe(SaleObserver::class);
        GoodReceiveNote::observe(GoodReceiveNoteObserver::class);
        Expense::observe(ExpenseObserver::class);
        SupplierPayment::observe(SupplierPaymentObserver::class);
    }
}
