<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customer::class);

        // Generate next member code
        $nextMemberCode = $this->generateNextMemberCode();

        return view('customers.create', compact('nextMemberCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        // Auto-generate member code if not provided
        if (empty($data['member_code'])) {
            $data['member_code'] = $this->generateNextMemberCode();
        }

        $customer = Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Nasabah berhasil ditambahkan.');
    }

    /**
     * Generate next member code
     */
    private function generateNextMemberCode(): string
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();

        if (! $lastCustomer) {
            return 'NSB-0001';
        }

        // Extract number from last code (e.g., NSB-0001 -> 1)
        $lastNumber = (int) substr($lastCustomer->member_code, 4);
        $nextNumber = $lastNumber + 1;

        return 'NSB-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        $customer->load([
            'ledgerEntries' => fn($q) => $q->latest()->limit(20),
            'deposits' => fn($q) => $q->latest()->limit(10),
            'withdrawals' => fn($q) => $q->latest()->limit(10),
        ]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Data nasabah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Nasabah berhasil dihapus.');
    }
}
