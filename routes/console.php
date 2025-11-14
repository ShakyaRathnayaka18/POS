<?php

use App\Models\SupplierCredit;
use App\Services\PaymentReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Supplier Credit Payment Reminders - Run daily
Schedule::call(function (PaymentReminderService $service) {
    $service->generateReminders();
})->daily()->at('09:00');

// Update Overdue Credit Statuses - Run daily
Schedule::call(function () {
    SupplierCredit::where('status', '!=', 'paid')
        ->where('due_date', '<', now())
        ->update(['status' => 'overdue']);
})->daily()->at('00:00');
