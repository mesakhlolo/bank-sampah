<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Support\DateRange;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Build the period summary used by both the HTML page and the PDF export.
     *
     * Previously `summary()` and `downloadPdf()` carried ~60 duplicated lines
     * each, so a fix to one silently diverged from the other.
     *
     * Date filtering goes through App\Support\DateRange instead of `whereDate()`.
     * `whereDate()` is correct but wraps the column in DATE(), which defeats the
     * index on `transaction_date`. A bare `Y-m-d` comparison restores the index but
     * is wrong here, because the models cast the column to `datetime` so stored
     * values are `'Y-m-d 00:00:00'` and a bare `<= 'Y-m-d'` drops the final day.
     * DateRange expands the bounds to real day boundaries, which is both
     * index-friendly and inclusive.
     *
     * @return array{
     *     start: string,
     *     end: string,
     *     totalDeposits: object,
     *     totalWithdrawals: object,
     *     netBalanceChange: float,
     *     categorySummary: \Illuminate\Support\Collection,
     *     topCustomers: \Illuminate\Support\Collection
     * }
     */
    public function buildSummary(?string $start = null, ?string $end = null): array
    {
        $start = $start ?: now()->startOfMonth()->format('Y-m-d');
        $end = $end ?: now()->endOfMonth()->format('Y-m-d');

        // Compare against real day boundaries, not bare `Y-m-d` strings. The column
        // is a DATE but the models cast it to `datetime`, so stored values look like
        // `2026-11-30 00:00:00`; a bare `<= '2026-11-30'` would drop the last day.
        // See App\Support\DateRange for the full explanation.
        $from = DateRange::startOfDay($start);
        $until = DateRange::endOfDay($end);

        // Total deposits
        $totalDeposits = Deposit::where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $until)
            ->where('status', 'final')
            ->selectRaw('SUM(total_amount) as total_amount, SUM(total_weight) as total_weight, COUNT(*) as count')
            ->first();

        // Total withdrawals
        $totalWithdrawals = Withdrawal::where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $until)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as count')
            ->first();

        // Net balance change
        $netBalanceChange = ($totalDeposits->total_amount ?? 0) - ($totalWithdrawals->total_amount ?? 0);

        // Breakdown by waste category
        $categorySummary = DB::table('deposit_items')
            ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
            ->where('deposits.transaction_date', '>=', $from)
            ->where('deposits.transaction_date', '<=', $until)
            ->where('deposits.status', 'final')
            ->select(
                'waste_categories.name',
                DB::raw('SUM(deposit_items.weight_kg) as total_weight'),
                DB::raw('SUM(deposit_items.subtotal) as total_amount'),
                DB::raw('COUNT(DISTINCT deposits.id) as transaction_count')
            )
            ->groupBy('waste_categories.id', 'waste_categories.name')
            ->orderByDesc('total_amount')
            ->get();

        // Top 10 customers by deposit amount
        $topCustomers = DB::table('deposits')
            ->join('customers', 'deposits.customer_id', '=', 'customers.id')
            ->where('deposits.transaction_date', '>=', $from)
            ->where('deposits.transaction_date', '<=', $until)
            ->where('deposits.status', 'final')
            ->select(
                'customers.name',
                'customers.phone',
                DB::raw('SUM(deposits.total_amount) as total_amount'),
                DB::raw('SUM(deposits.total_weight) as total_weight'),
                DB::raw('COUNT(deposits.id) as transaction_count')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        return compact(
            'start',
            'end',
            'totalDeposits',
            'totalWithdrawals',
            'netBalanceChange',
            'categorySummary',
            'topCustomers'
        );
    }
}
