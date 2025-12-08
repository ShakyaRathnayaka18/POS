<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\GoodReceiveNote;
use App\Models\PayrollPeriod;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\SupplierPayment;
use App\Observers\ExpenseObserver;
use App\Observers\GoodReceiveNoteObserver;
use App\Observers\PayrollPeriodObserver;
use App\Observers\SaleObserver;
use App\Observers\StockAdjustmentObserver;
use App\Observers\SupplierPaymentObserver;
use Illuminate\Support\Facades\URL;
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
    {     Schema::defaultStringLength(191);

 // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

         // Register observers for accounting integration
        Sale::observe(SaleObserver::class);
        GoodReceiveNote::observe(GoodReceiveNoteObserver::class);
        Expense::observe(ExpenseObserver::class);
        SupplierPayment::observe(SupplierPaymentObserver::class);
        PayrollPeriod::observe(PayrollPeriodObserver::class);
        StockAdjustment::observe(StockAdjustmentObserver::class);
    }
}
