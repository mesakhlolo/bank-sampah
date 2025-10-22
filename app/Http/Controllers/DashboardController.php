<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        // Metrics hari ini
        $todayDeposits = Deposit::whereDate('created_at', $today)
            ->where('status', 'final')
            ->sum('total_amount');

        $todayWithdrawals = Withdrawal::whereDate('created_at', $today)
            ->sum('amount');

        $todayTransactions = Deposit::whereDate('created_at', $today)
            ->where('status', 'final')
            ->count() +
            Withdrawal::whereDate('created_at', $today)
                ->count();

        // Top 3 kategori hari ini (by weight & amount)
        $topCategoriesToday = DB::table('deposit_items')
            ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
            ->whereDate('deposits.created_at', $today)
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
