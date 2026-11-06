<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\UniqueConstraintViolationException;
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
        // Retry on "database is locked" so SQLite, which ignores lockForUpdate(),
        // re-runs the balance check after the competing writer commits.
        return DB::transaction(function () use ($data, $user) {
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new \RuntimeException('Nominal penarikan harus lebih besar dari nol.');
            }

            // Lock the customer row before reading the balance. On MySQL/MariaDB
            // this serialises concurrent withdrawals for the same customer, so two
            // requests cannot both pass the check and overdraw the ledger.
            // SQLite ignores lockForUpdate(); the transaction retry above covers it.
            $customer = Customer::query()
                ->whereKey($data['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (round($customer->balance, 2) < round($amount, 2)) {
                throw new \RuntimeException('Saldo tidak mencukupi untuk penarikan ini.');
            }

            // Create withdrawal, retrying on code collision
            $withdrawal = $this->createWithUniqueCode($data, $user);

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
        }, 5);
    }

    /**
     * Create the withdrawal row, retrying when the generated code collides.
     *
     * @param  array<string, mixed>  $data
     */
    private function createWithUniqueCode(array $data, User $user): Withdrawal
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return Withdrawal::create([
                    'code' => $this->generateWithdrawalCode(),
                    'customer_id' => $data['customer_id'],
                    'amount' => $data['amount'],
                    'transaction_date' => $data['transaction_date'],
                    'note' => $data['note'] ?? null,
                    'created_by' => $user->id,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
            }
        }

        // Unreachable: the loop either returns or throws.
        throw new \RuntimeException('Gagal membuat kode penarikan unik.');
    }

    /**
     * Generate unique withdrawal code
     * Format: WD-YYYYMMDD-####
     */
    private function generateWithdrawalCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "WD-{$date}-";

        // Lock the highest existing code for this day so concurrent transactions
        // serialize instead of computing the same next sequence number.
        // NOTE: when no row exists yet there is nothing to lock — the unique
        // constraint plus the retry in createWithUniqueCode() covers that case.
        $lastWithdrawal = Withdrawal::where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->lockForUpdate()
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
