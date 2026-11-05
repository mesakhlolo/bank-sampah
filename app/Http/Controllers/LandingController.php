<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\WasteCategory;
use App\Models\Withdrawal;

class LandingController extends Controller
{
    /**
     * Landing page
     */
    public function index()
    {
        // Calculate statistics
        $stats = [
            'total_customers' => Customer::count(),
            'total_weight' => round(Deposit::sum('total_weight'), 2), // in kg
            'total_amount' => Withdrawal::sum('amount'),
            'trees_equivalent' => round(Deposit::sum('total_weight') / 100), // 1 ton = 10 trees
        ];

        // Get active waste categories with current prices
        $wasteCategories = WasteCategory::with(['wastePrices' => function ($query) {
            $query->where('effective_from', '<=', now())
                ->latest('effective_from')
                ->limit(1);
        }])
            ->where('is_active', true)
            ->limit(6)
            ->get();

        return view('landing.index', compact('stats', 'wasteCategories'));
    }
}
