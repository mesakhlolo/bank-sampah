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

        // Build deposits query - only show deposits, not withdrawals
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

        // Sort by date descending and paginate
        $deposits = $depositsQuery->orderBy('transaction_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('deposits.index', [
            'deposits' => $deposits,
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
