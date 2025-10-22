<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Total deposits
        $totalDeposits = Deposit::whereBetween('created_at', [$start, $end])
            ->where('status', 'final')
            ->selectRaw('SUM(total_amount) as total_amount, SUM(total_weight) as total_weight, COUNT(*) as count')
            ->first();

        // Total withdrawals
        $totalWithdrawals = Withdrawal::whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as count')
            ->first();

        // Net balance change
        $netBalanceChange = ($totalDeposits->total_amount ?? 0) - ($totalWithdrawals->total_amount ?? 0);

        // Breakdown by waste category
        $categorySummary = DB::table('deposit_items')
            ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
            ->whereBetween('deposits.created_at', [$start, $end])
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
            ->whereBetween('deposits.created_at', [$start, $end])
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

        return view('reports.summary', compact(
            'start',
            'end',
            'totalDeposits',
            'totalWithdrawals',
            'netBalanceChange',
            'categorySummary',
            'topCustomers'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Reuse same logic as summary method
        $totalDeposits = Deposit::whereBetween('created_at', [$start, $end])
            ->where('status', 'final')
            ->selectRaw('SUM(total_amount) as total_amount, SUM(total_weight) as total_weight, COUNT(*) as count')
            ->first();

        $totalWithdrawals = Withdrawal::whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as count')
            ->first();

        $netBalanceChange = ($totalDeposits->total_amount ?? 0) - ($totalWithdrawals->total_amount ?? 0);

        $categorySummary = DB::table('deposit_items')
            ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
            ->whereBetween('deposits.created_at', [$start, $end])
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

        $topCustomers = DB::table('deposits')
            ->join('customers', 'deposits.customer_id', '=', 'customers.id')
            ->whereBetween('deposits.created_at', [$start, $end])
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

        $pdf = Pdf::loadView('reports.summary-pdf', compact(
            'start',
            'end',
            'totalDeposits',
            'totalWithdrawals',
            'netBalanceChange',
            'categorySummary',
            'topCustomers'
        ));

        return $pdf->download("Laporan-Ringkasan-{$start}-{$end}.pdf");
    }
}
