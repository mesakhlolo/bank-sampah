<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class LedgerController extends Controller
{
    public function show(Customer $customer)
    {
        // Get all ledger entries with relationships eager loaded
        $ledgerEntries = $customer->ledgerEntries()
            ->with(['referenceable'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate running balance
        $runningBalance = 0;
        $ledgerWithBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
            if ($entry->direction === 'DEBIT') {
                $runningBalance += $entry->amount;
            } else {
                $runningBalance -= $entry->amount;
            }

            return [
                'entry' => $entry,
                'running_balance' => $runningBalance,
            ];
        });

        return view('ledger.show', compact('customer', 'ledgerWithBalance'));
    }
}
