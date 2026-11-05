<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    /**
     * SQL expression for one entry's effect on the balance: KREDIT adds, DEBIT
     * subtracts. Kept here so the sign convention lives in exactly one place.
     *
     * This is the opposite of a conventional bank statement (where a debit to the
     * customer's account increases it); see docs/PROJECT.md §2.
     */
    public const SIGNED_AMOUNT_SQL = "CASE WHEN direction = 'KREDIT' THEN amount ELSE -amount END";

    /**
     * SQL expression that turns a row set into a signed balance total.
     *
     * Used by `withAggregate()` sub-selects and by `Customer::getBalanceAttribute()`.
     */
    public const BALANCE_SUM_SQL = 'COALESCE(SUM('.self::SIGNED_AMOUNT_SQL.'), 0)';

    protected $fillable = [
        'customer_id',
        'ref_type',
        'ref_id',
        'direction',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the customer for this ledger entry
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the referenceable model (Deposit or Withdrawal)
     */
    public function referenceable()
    {
        return $this->morphTo('referenceable', 'ref_type', 'ref_id');
    }

    /**
     * Scope for debit entries
     */
    public function scopeDebit($query)
    {
        return $query->where('direction', 'DEBIT');
    }

    /**
     * Scope for kredit entries
     */
    public function scopeKredit($query)
    {
        return $query->where('direction', 'KREDIT');
    }
}
