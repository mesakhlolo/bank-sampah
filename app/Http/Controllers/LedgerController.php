<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class LedgerController extends Controller
{
    public function show(Customer $customer)
    {
        // Running balance comes from a window function in SQL, so the ledger is not
        // pulled into PHP just to accumulate it.
        //
        // Note: the previous implementation added DEBIT and subtracted KREDIT,
        // which is inverted for this ledger (deposit = KREDIT = balance up).
        // See docs/PROJECT.md §2.
        $ledgerWithBalance = $customer->ledgerWithRunningBalance()
            ->paginate(20)
            ->withQueryString();

        return view('ledger.show', compact('customer', 'ledgerWithBalance'));
    }
}
