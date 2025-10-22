<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_id',
        'transaction_date',
        'total_amount',
        'total_weight',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'total_weight' => 'decimal:2',
    ];

    /**
     * Get the customer for this deposit
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created this deposit
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get deposit items
     */
    public function items()
    {
        return $this->hasMany(DepositItem::class);
    }

    /**
     * Get ledger entry for this deposit
     */
    public function ledgerEntry()
    {
        return $this->morphOne(LedgerEntry::class, 'referenceable', 'ref_type', 'ref_id');
    }

    /**
     * Generate unique deposit code
     */
    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $latest = self::where('code', 'like', "DEP-{$date}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "DEP-{$date}-{$newNumber}";
    }

    /**
     * Scope for final deposits
     */
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    /**
     * Scope for draft deposits
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
