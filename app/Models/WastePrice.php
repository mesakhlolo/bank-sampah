<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WastePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'waste_category_id',
        'price_per_kg',
        'effective_from',
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'effective_from' => 'datetime',
    ];

    /**
     * Get the category this price belongs to
     */
    public function wasteCategory()
    {
        return $this->belongsTo(WasteCategory::class);
    }

    /**
     * Scope to get prices active on a specific date
     */
    public function scopeActiveOn($query, $date)
    {
        return $query->where('effective_from', '<=', $date)
            ->orderBy('effective_from', 'desc');
    }
}
