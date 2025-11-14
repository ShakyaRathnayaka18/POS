<?php

namespace App\Models;

use App\Enums\ReminderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    protected $fillable = [
        'supplier_credit_id',
        'reminder_type',
        'days_before_due',
        'days_overdue',
        'sent_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'status' => ReminderStatusEnum::class,
        ];
    }

    public function supplierCredit(): BelongsTo
    {
        return $this->belongsTo(SupplierCredit::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'sent_at' => now(),
            'status' => ReminderStatusEnum::SENT,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => ReminderStatusEnum::FAILED,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', ReminderStatusEnum::PENDING);
    }

    public function scopeSent($query)
    {
        return $query->where('status', ReminderStatusEnum::SENT);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', ReminderStatusEnum::FAILED);
    }
}
