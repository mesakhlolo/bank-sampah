<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use App\Support\DateRange;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    protected WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * Display a listing of withdrawals
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['customer', 'creator']);

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range. Real day boundaries keep the index usable while
        // still including the final day. See App\Support\DateRange.
        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', DateRange::startOfDay($request->start_date));
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', DateRange::endOfDay($request->end_date));
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%'.$request->search.'%');
        }

        $withdrawals = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(15);

        $customers = Customer::orderBy('name')->get();

        return view('withdrawals.index', compact('withdrawals', 'customers'));
    }

    /**
     * Show the form for creating a new withdrawal
     */
    public function create()
    {
        // Balances come from the ledger, so aggregate them in one query instead of
        // reading `$customer->balance` inside a map (that was one query per customer).
        $customers = Customer::query()
            ->select('id', 'member_code', 'name')
            ->withAggregate('ledgerEntries as ledger_sum_signed', LedgerEntry::BALANCE_SUM_SQL)
            ->orderBy('name')
            ->get()
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                // `member_code` is the internal code; `code` is not a column on customers.
                'code' => $customer->member_code,
                'name' => $customer->name,
                'balance' => (float) ($customer->ledger_sum_signed ?? 0),
            ]);

        return view('withdrawals.create', compact('customers'));
    }

    /**
     * Store a newly created withdrawal
     */
    public function store(StoreWithdrawalRequest $request)
    {
        try {
            $withdrawal = $this->withdrawalService->createWithdrawal(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('withdrawals.show', $withdrawal)
                ->with('success', 'Penarikan berhasil dibuat dengan kode: '.$withdrawal->code);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified withdrawal
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['customer', 'creator']);

        return view('withdrawals.show', compact('withdrawal'));
    }

    /**
     * Download withdrawal receipt as PDF
     */
    public function downloadPdf(Withdrawal $withdrawal)
    {
        $withdrawal->load(['customer', 'creator']);

        $pdf = Pdf::loadView('withdrawals.receipt-pdf', compact('withdrawal'));

        return $pdf->download('Bukti-Penarikan-'.$withdrawal->code.'.pdf');
    }
}
