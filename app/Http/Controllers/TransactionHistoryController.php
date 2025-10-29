<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table(DB::raw('(
            SELECT 
                "deposit" as type,
                id,
                code,
                customer_id,
                transaction_date,
                total_amount as amount,
                created_by,
                created_at
            FROM deposits
            UNION ALL
            SELECT 
                "withdrawal" as type,
                id,
                code,
                customer_id,
                transaction_date,
                amount,
                created_by,
                created_at
            FROM withdrawals
        ) as transactions'));

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by transaction type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'LIKE', '%'.$request->search.'%');
        }

        // Get paginated results
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->all());

        // Load related data
        $transactionsWithRelations = collect($transactions->items())->map(function ($transaction) {
            $transaction->customer = Customer::find($transaction->customer_id);
            $transaction->creator = \App\Models\User::find($transaction->created_by);

            return $transaction;
        });

        // Replace items with enhanced data
        $transactions->setCollection($transactionsWithRelations);

        // Get all customers for filter
        $customers = Customer::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'customers'));
    }
}
