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

        $customers = Customer::all();
        $categories = WasteCategory::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->command->error('No active waste categories found!');

            return;
        }

        $this->command->info('Creating deposit transactions...');
        
        $depositCount = 0;
        
        foreach ($customers as $customer) {
            // Create 3-8 deposits per customer untuk data yang lebih banyak
            $depositPerCustomer = rand(3, 8);

            for ($i = 0; $i < $depositPerCustomer; $i++) {
                $items = [];

                // Random 1-4 categories per deposit
                $categoryCount = rand(1, 4);
                $selectedCategories = $categories->random(min($categoryCount, $categories->count()));

                foreach ($selectedCategories as $category) {
                    $items[] = [
                        'waste_category_id' => $category->id,
                        'weight_kg' => rand(5, 500) / 10, // 0.5 - 50.0 kg
                    ];
                }

                // Create deposits spread over the last 60 days
                $data = [
                    'customer_id' => $customer->id,
                    'created_by' => $operator->id,
                    'transaction_date' => now()->subDays(rand(0, 60))->format('Y-m-d'),
                    'items' => $items,
                    'notes' => fake()->optional(0.3)->randomElement([
                        'Setoran rutin',
                        'Hasil pemilahan minggu ini',
                        'Sampah dari kantor',
                        'Kumpulan sampah rumah tangga',
                        'Setoran dari lingkungan RT',
                    ]),
                ];

                try {
                    $deposit = $depositService->createDeposit($data);
                    $depositCount++;
                    
                    if ($depositCount % 20 == 0) {
                        $this->command->info("Progress: {$depositCount} deposits created...");
                    }
                } catch (\Exception $e) {
                    $this->command->error("✗ Error creating deposit for {$customer->name}: ".$e->getMessage());
                }
            }
        }

        $this->command->info("✓ Total deposits created: {$depositCount}");
        
        // Show some statistics
        $totalAmount = \App\Models\Deposit::sum('total_amount');
        $this->command->info("✓ Total deposit amount: Rp ".number_format($totalAmount, 0, ',', '.'));
    }
}
