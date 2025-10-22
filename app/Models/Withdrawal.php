<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_id',
        'amount',
        'transaction_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the customer for this withdrawal
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created this withdrawal
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get ledger entry for this withdrawal
     */
    public function ledgerEntry()
    {
        return $this->morphOne(LedgerEntry::class, 'referenceable', 'ref_type', 'ref_id');
    }

    /**
     * Generate unique withdrawal code
     */
    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $latest = self::where('code', 'like', "WD-{$date}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "WD-{$date}-{$newNumber}";
    }
}
