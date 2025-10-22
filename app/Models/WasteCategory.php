<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get prices for this category
     */
    public function prices()
    {
        return $this->hasMany(WastePrice::class);
    }

    /**
     * Get deposit items for this category
     */
    public function depositItems()
    {
        return $this->hasMany(DepositItem::class);
    }

    /**
     * Get the active price for a specific date
     */
    public function getActivePriceOn($date = null)
    {
        $date = $date ?? now();

        return $this->prices()
            ->where('effective_from', '<=', $date)
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    /**
     * Get the current active price
     */
    public function getCurrentPriceAttribute()
    {
        $activePrice = $this->getActivePriceOn();

        return $activePrice ? $activePrice->price_per_kg : 0;
    }

    /**
     * Scope active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
