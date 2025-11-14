<?php

namespace App\Models;

use App\Enums\CreditStatusEnum;
use App\Enums\CreditTermsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierCredit extends Model
{
    protected $fillable = [
        'credit_number',
        'supplier_id',
        'good_receive_note_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'credit_terms',
        'credit_days',
        'original_amount',
        'paid_amount',
        'outstanding_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'original_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
            'status' => CreditStatusEnum::class,
            'credit_terms' => CreditTermsEnum::class,
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function goodReceiveNote(): BelongsTo
    {
        return $this->belongsTo(GoodReceiveNote::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(PaymentReminder::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', CreditStatusEnum::PENDING);
    }

    public function scopePartial($query)
    {
        return $query->where('status', CreditStatusEnum::PARTIAL);
    }

    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', CreditStatusEnum::OVERDUE)
                ->orWhere(function ($q2) {
                    $q2->where('due_date', '<', now())
                        ->whereNotIn('status', [CreditStatusEnum::PAID]);
                });
        });
    }

    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now())
            ->whereNotIn('status', [CreditStatusEnum::PAID]);
    }

    public function scopeBySupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function getRemainingDaysAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function getAgingDaysAttribute(): int
    {
        if ($this->due_date->isFuture()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->original_amount == 0) {
            return 0;
        }

        return ($this->paid_amount / $this->original_amount) * 100;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== CreditStatusEnum::PAID;
    }

    public function isDueSoon(int $days = 7): bool
    {
        return $this->due_date->isBetween(now(), now()->addDays($days))
               && $this->status !== CreditStatusEnum::PAID;
    }

    public function canMakePayment(): bool
    {
        return $this->outstanding_amount > 0 && $this->status !== CreditStatusEnum::PAID;
    }

    public function updateStatus(): void
    {
        if ($this->outstanding_amount <= 0) {
            $this->status = CreditStatusEnum::PAID;
        } elseif ($this->paid_amount > 0) {
            $this->status = CreditStatusEnum::PARTIAL;
        } elseif ($this->isOverdue()) {
            $this->status = CreditStatusEnum::OVERDUE;
        } else {
            $this->status = CreditStatusEnum::PENDING;
        }

        $this->save();
    }

    public static function generateCreditNumber(): string
    {
        $lastCredit = self::orderBy('id', 'desc')->first();
        $sequence = $lastCredit ? (int) substr($lastCredit->credit_number, 4) + 1 : 1;

        return 'CRD-'.str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
