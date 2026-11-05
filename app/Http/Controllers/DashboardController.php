<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // `created_at` is a timestamp, so "today" is expressed as a half-open
        // interval: >= start of today and < start of tomorrow. This keeps the index
        // on `created_at` usable (`whereDate()` wrapped the column in DATE() and
        // defeated it) and avoids the microsecond truncation that a closed
        // `endOfDay()` bound can suffer.
        $startOfDay = now()->startOfDay();
        $endOfDay = $startOfDay->copy()->addDay();

        // Metrics hari ini
        $todayDeposits = Deposit::where('created_at', '>=', $startOfDay)
            ->where('created_at', '<', $endOfDay)
            ->where('status', 'final')
            ->sum('total_amount');

        $todayWithdrawals = Withdrawal::where('created_at', '>=', $startOfDay)
            ->where('created_at', '<', $endOfDay)
            ->sum('amount');

        $todayTransactions = Deposit::where('created_at', '>=', $startOfDay)
            ->where('created_at', '<', $endOfDay)
            ->where('status', 'final')
            ->count() +
            Withdrawal::where('created_at', '>=', $startOfDay)
                ->where('created_at', '<', $endOfDay)
                ->count();

        // Top 3 kategori hari ini (by weight & amount)
        $topCategoriesToday = DB::table('deposit_items')
            ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
            ->where('deposits.created_at', '>=', $startOfDay)
            ->where('deposits.created_at', '<', $endOfDay)
            ->where('deposits.status', 'final')
            ->select(
                'waste_categories.name',
                DB::raw('SUM(deposit_items.weight_kg) as total_weight'),
                DB::raw('SUM(deposit_items.subtotal) as total_amount')
            )
            ->groupBy('waste_categories.id', 'waste_categories.name')
            ->orderByDesc('total_weight')
            ->limit(3)
            ->get();

        return view('dashboard', compact(
            'todayDeposits',
            'todayWithdrawals',
            'todayTransactions',
            'topCategoriesToday'
        ));
    }
}
