<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type_id',
        'parent_account_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function accountBalances(): HasMany
    {
        return $this->hasMany(AccountBalance::class);
    }
}
