<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class LedgerController extends Controller
{
    public function show(Customer $customer)
    {
        // Get all ledger entries with relationships eager loaded
        // Order by DESC (newest first) for display
        $ledgerEntries = $customer->ledgerEntries()
            ->with(['referenceable'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate running balance from oldest to newest first
        $entriesForBalance = $customer->ledgerEntries()
            ->orderBy('created_at', 'asc')
            ->get();

        $balances = [];
        $runningBalance = 0;

        foreach ($entriesForBalance as $entry) {
            if ($entry->direction === 'DEBIT') {
                $runningBalance += $entry->amount;
            } else {
                $runningBalance -= $entry->amount;
            }
            $balances[$entry->id] = $runningBalance;
        }

        // Map the DESC ordered entries with their calculated balances
        $ledgerWithBalance = $ledgerEntries->map(function ($entry) use ($balances) {
            return [
                'entry' => $entry,
                'running_balance' => $balances[$entry->id],
            ];
        });

        return view('ledger.show', compact('customer', 'ledgerWithBalance'));
    }
}
