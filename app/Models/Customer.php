<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'unique_code',
        'name',
        'phone',
        'address',
        'joined_at',
        'last_portal_access',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate member_code and unique_code before creating if not provided
        static::creating(function ($customer) {
            if (empty($customer->member_code)) {
                $customer->member_code = static::generateNextMemberCode();
            }

            if (empty($customer->unique_code)) {
                $customer->unique_code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate next member code (sequential, for admin/internal use)
     */
    private static function generateNextMemberCode(): string
    {
        // Get the last customer by ID to ensure proper sequencing
        $lastCustomer = static::orderBy('id', 'desc')->lockForUpdate()->first();

        if (! $lastCustomer) {
            return 'CUST-0001';
        }

        // Extract number from last code using regex (handles CUST-xxxx or any other format)
        preg_match('/(\d+)$/', $lastCustomer->member_code, $matches);

        if (isset($matches[1])) {
            $lastNumber = (int) $matches[1];
        } else {
            // Fallback: use the ID as base
            $lastNumber = $lastCustomer->id;
        }

        $nextNumber = $lastNumber + 1;

        // Use minimum 4 digits, but allow expansion beyond 9999
        return 'CUST-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 🔒 Generate secure random unique code (for portal public access)
     * Format: BSB-XXXXXXXXXXXX (12 random chars)
     * Total kombinasi: 36^12 = 4,738,381,338,321,616,896
     */
    private static function generateUniqueCode(): string
    {
        do {
            // Cryptographically secure random string
            $code = 'BSB-'.strtoupper(Str::random(12));

            // Check collision (very unlikely, but safety first)
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }

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
