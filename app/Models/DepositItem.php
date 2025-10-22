<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'waste_category_id',
        'weight_kg',
        'price_per_kg',
        'subtotal',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the deposit this item belongs to
     */
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    /**
     * Get the waste category
     */
    public function wasteCategory()
    {
        return $this->belongsTo(WasteCategory::class);
    }
}
