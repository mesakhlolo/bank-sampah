<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'name',
        'phone',
        'address',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Get deposits for this customer
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get withdrawals for this customer
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get ledger entries for this customer
     */
    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    /**
     * Calculate customer balance from ledger entries
     */
    public function getBalanceAttribute(): float
    {
        return $this->ledgerEntries()
            ->get()
            ->sum(function ($entry) {
                return $entry->direction === 'KREDIT' ? $entry->amount : -$entry->amount;
            });
    }

    /**
     * Get recent transactions
     */
    public function recentTransactions($limit = 10)
    {
        return $this->ledgerEntries()
            ->with(['referenceable'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
