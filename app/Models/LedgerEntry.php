<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

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
