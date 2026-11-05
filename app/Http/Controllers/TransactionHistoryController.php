<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Support\DateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table(DB::raw('(
            SELECT
                \'deposit\' as type,
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
                \'withdrawal\' as type,
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

        // Filter by date range. The union sub-select exposes the raw column value,
        // which is stored as `Y-m-d H:i:s` because the models cast it to datetime,
        // so the boundaries must be real day boundaries rather than bare dates.
        // See App\Support\DateRange.
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', DateRange::startOfDay($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', DateRange::endOfDay($request->date_to));
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

        // Resolve customers and creators in two queries for the whole page.
        // The previous version called Customer::find() and User::find() inside the
        // map, which cost 2 queries per row (30 per page).
        $items = collect($transactions->items());

        $customers = Customer::whereIn('id', $items->pluck('customer_id')->unique()->filter())
            ->get()
            ->keyBy('id');

        $creators = User::whereIn('id', $items->pluck('created_by')->unique()->filter())
            ->get()
            ->keyBy('id');

        $transactionsWithRelations = $items->map(function ($transaction) use ($customers, $creators) {
            $transaction->customer = $customers->get($transaction->customer_id);
            $transaction->creator = $creators->get($transaction->created_by);

            return $transaction;
        });

        // Replace items with enhanced data
        $transactions->setCollection($transactionsWithRelations);

        // Get all customers for filter
        $customers = Customer::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'customers'));
    }
}
