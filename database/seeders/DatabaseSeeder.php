<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();
        
        $this->call([
            UserSeeder::class,
            WasteCategorySeeder::class,
            WastePriceSeeder::class,
            CustomerSeeder::class,
            DepositSeeder::class,
            WithdrawalSeeder::class,
        ]);
        
        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        
        // Display summary statistics
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users', \App\Models\User::count()],
                ['Customers', \App\Models\Customer::count()],
                ['Waste Categories', \App\Models\WasteCategory::count()],
                ['Waste Prices', \App\Models\WastePrice::count()],
                ['Deposits', \App\Models\Deposit::count()],
                ['Deposit Items', \App\Models\DepositItem::count()],
                ['Withdrawals', \App\Models\Withdrawal::count()],
                ['Ledger Entries', \App\Models\LedgerEntry::count()],
            ]
        );
    }
}
