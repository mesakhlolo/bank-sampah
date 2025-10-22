<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\WasteCategory;
use App\Services\DepositService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    use AuthorizesRequests;

    protected $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    /**
     * Display a listing of deposits
     */
    public function index(Request $request)
    {
        // Get all customers for filter
        $customers = Customer::orderBy('name')->get();

        // Build combined transactions query
        $type = $request->input('type', ''); // 'deposit', 'withdrawal', or '' for all

        $transactions = collect();

        // Get deposits
        if ($type === '' || $type === 'deposit') {
            $depositsQuery = Deposit::with(['customer', 'creator'])
                ->where('status', 'final');

            // Apply filters
            if ($request->filled('customer_id')) {
                $depositsQuery->where('customer_id', $request->customer_id);
            }
            if ($request->filled('date_from')) {
                $depositsQuery->whereDate('transaction_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $depositsQuery->whereDate('transaction_date', '<=', $request->date_to);
            }
            if ($request->filled('search')) {
                $depositsQuery->where('code', 'like', '%'.$request->search.'%');
            }

            $deposits = $depositsQuery->get()->map(function ($deposit) {
                return (object) [
                    'id' => $deposit->id,
                    'type' => 'deposit',
                    'code' => $deposit->code,
                    'date' => $deposit->transaction_date ?? $deposit->created_at,
                    'customer_name' => $deposit->customer->name,
                    'creator_name' => $deposit->creator->name,
                    'amount' => $deposit->total_amount,
                ];
            });

            $transactions = $transactions->merge($deposits);
        }

        // Get withdrawals
        if ($type === '' || $type === 'withdrawal') {
            $withdrawalsQuery = \App\Models\Withdrawal::with(['customer', 'creator']);

            // Apply filters
            if ($request->filled('customer_id')) {
                $withdrawalsQuery->where('customer_id', $request->customer_id);
            }
            if ($request->filled('date_from')) {
                $withdrawalsQuery->whereDate('transaction_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $withdrawalsQuery->whereDate('transaction_date', '<=', $request->date_to);
            }
            if ($request->filled('search')) {
                $withdrawalsQuery->where('code', 'like', '%'.$request->search.'%');
            }

            $withdrawals = $withdrawalsQuery->get()->map(function ($withdrawal) {
                return (object) [
                    'id' => $withdrawal->id,
                    'type' => 'withdrawal',
                    'code' => $withdrawal->code,
                    'date' => $withdrawal->transaction_date ?? $withdrawal->created_at,
                    'customer_name' => $withdrawal->customer->name,
                    'creator_name' => $withdrawal->creator->name,
                    'amount' => $withdrawal->amount,
                ];
            });

            $transactions = $transactions->merge($withdrawals);
        }

        // Sort by date descending
        $transactions = $transactions->sortByDesc('date');

        // Paginate manually
        $page = $request->input('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->slice($offset, $perPage)->values(),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('deposits.index', [
            'transactions' => $paginatedTransactions,
            'customers' => $customers,
        ]);
    }

    /**
     * Show the form for creating a new deposit
     */
    public function create()
    {
        $this->authorize('create', Deposit::class);

        $customers = Customer::orderBy('name')->get();
        $wasteCategories = WasteCategory::active()->with('prices')->get();

        return view('deposits.create', compact('customers', 'wasteCategories'));
    }

    /**
     * Store a newly created deposit
     */
    public function store(StoreDepositRequest $request)
    {
        $this->authorize('create', Deposit::class);

        try {
            $deposit = $this->depositService->createDeposit([
                'customer_id' => $request->customer_id,
                'created_by' => auth()->id(),
                'transaction_date' => $request->transaction_date ?? now(),
                'notes' => $request->notes,
                'items' => $request->items,
            ]);

            return redirect()->route('deposits.show', $deposit)
                ->with('success', 'Transaksi setoran berhasil disimpan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: '.$e->getMessage());
        }
    }

    /**
     * Display the specified deposit
     */
    public function show(Deposit $deposit)
    {
        $deposit->load(['customer', 'creator', 'items.wasteCategory']);

        return view('deposits.show', compact('deposit'));
    }

    /**
     * Generate PDF receipt for the deposit
     */
    public function downloadPdf(Deposit $deposit)
    {
        $deposit->load(['customer', 'creator', 'items.wasteCategory']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('deposits.receipt-pdf', compact('deposit'));

        return $pdf->download("Struk-{$deposit->code}.pdf");
    }
}
