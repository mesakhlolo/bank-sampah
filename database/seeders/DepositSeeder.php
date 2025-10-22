<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\WasteCategory;
use App\Services\DepositService;
use Illuminate\Database\Seeder;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $depositService = new DepositService;
        $operator = User::where('role', 'operator')->first();

        if (! $operator) {
            $this->command->error('Operator user not found!');

            return;
        }

        $customers = Customer::take(5)->get();
        $categories = WasteCategory::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->command->error('No active waste categories found!');

            return;
        }

        foreach ($customers as $customer) {
            // Create 1-2 deposits per customer
            $depositCount = rand(1, 2);

            for ($i = 0; $i < $depositCount; $i++) {
                $items = [];

                // Random 2-3 categories per deposit
                $selectedCategories = $categories->random(rand(2, 3));

                foreach ($selectedCategories as $category) {
                    $items[] = [
                        'waste_category_id' => $category->id,
                        'weight_kg' => rand(10, 100) / 10, // 1.0 - 10.0 kg
                    ];
                }

                $data = [
                    'customer_id' => $customer->id,
                    'created_by' => $operator->id,
                    'transaction_date' => now()->subDays(rand(0, 14))->format('Y-m-d'),
                    'items' => $items,
                    'notes' => fake()->optional(0.3)->sentence(),
                ];

                try {
                    $deposit = $depositService->createDeposit($data);
                    $this->command->info("✓ Deposit {$deposit->code} created for {$customer->name} (Rp ".number_format($deposit->total_amount, 0, ',', '.').')');
                } catch (\Exception $e) {
                    $this->command->error("✗ Error creating deposit for {$customer->name}: ".$e->getMessage());
                }
            }
        }

        $this->command->info("\nTotal deposits created: ".\App\Models\Deposit::count());
    }
}
