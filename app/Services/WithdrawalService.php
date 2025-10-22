<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    /**
     * Create a new withdrawal transaction
     *
     * @param  array  $data  Must contain: customer_id, amount, transaction_date
     * @param  User  $user  The user creating the withdrawal
     *
     * @throws \Exception
     */
    public function createWithdrawal(array $data, User $user): Withdrawal
    {
        return DB::transaction(function () use ($data, $user) {
            // Load customer with fresh balance
            $customer = Customer::findOrFail($data['customer_id']);

            // Validate balance
            if ($customer->balance < $data['amount']) {
                throw new \Exception('Saldo tidak mencukupi untuk penarikan ini.');
            }

            // Generate unique code
            $code = $this->generateWithdrawalCode();

            // Create withdrawal
            $withdrawal = Withdrawal::create([
                'code' => $code,
                'customer_id' => $data['customer_id'],
                'amount' => $data['amount'],
                'transaction_date' => $data['transaction_date'],
                'note' => $data['note'] ?? null,
                'created_by' => $user->id,
            ]);

            // Create ledger entry (DEBIT = mengurangi saldo)
            LedgerEntry::create([
                'customer_id' => $data['customer_id'],
                'ref_type' => Withdrawal::class,
                'ref_id' => $withdrawal->id,
                'direction' => 'DEBIT',
                'amount' => $data['amount'],
            ]);

            // Load relationships for display
            $withdrawal->load(['customer', 'creator']);

            return $withdrawal;
        });
    }

    /**
     * Generate unique withdrawal code
     * Format: WD-YYYYMMDD-####
     */
    private function generateWithdrawalCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "WD-{$date}-";

        // Get the last withdrawal code for today
        $lastWithdrawal = Withdrawal::where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastWithdrawal) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastWithdrawal->code, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix.str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
