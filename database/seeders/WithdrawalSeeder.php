<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Services\WithdrawalService;
use Illuminate\Database\Seeder;

class WithdrawalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $withdrawalService = new WithdrawalService;
        $operator = User::where('role', 'operator')->first();

        if (! $operator) {
            $this->command->error('Operator user not found!');

            return;
        }

        // Get customers who have balance
        $customers = Customer::whereHas('ledgerEntries')
            ->get()
            ->filter(function ($customer) {
                return $customer->balance > 50000; // Only customers with balance > 50k
            });

        if ($customers->isEmpty()) {
            $this->command->info('No customers with sufficient balance found. Skipping withdrawals.');

            return;
        }

        $withdrawalCount = 0;

        foreach ($customers as $customer) {
            // 60% chance to create withdrawal
            if (rand(1, 100) <= 60) {
                // Withdraw between 30% to 70% of balance
                $withdrawAmount = rand(30, 70) * $customer->balance / 100;
                $withdrawAmount = floor($withdrawAmount / 10000) * 10000; // Round to nearest 10k

                if ($withdrawAmount < 10000) {
                    continue;
                }

                $data = [
                    'customer_id' => $customer->id,
                    'amount' => $withdrawAmount,
                    'transaction_date' => now()->subDays(rand(0, 60))->format('Y-m-d'),
                    'note' => fake()->optional(0.4)->randomElement([
                        'Penarikan tunai',
                        'Transfer bank',
                        'Kebutuhan mendadak',
                        'Penarikan rutin',
                    ]),
                ];

                try {
                    $withdrawal = $withdrawalService->createWithdrawal($data, $operator);
                    $withdrawalCount++;
                    $this->command->info("✓ Withdrawal {$withdrawal->code} created for {$customer->name} (Rp ".number_format($withdrawal->amount, 0, ',', '.').')');
                } catch (\Exception $e) {
                    $this->command->error("✗ Error creating withdrawal for {$customer->name}: ".$e->getMessage());
                }
            }
        }

        $this->command->info("\nTotal withdrawals created: {$withdrawalCount}");
    }
}
