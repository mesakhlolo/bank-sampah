<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class GenerateUniqueCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:generate-unique-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique codes for customers who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = Customer::whereNull('unique_code')->get();

        if ($customers->isEmpty()) {
            $this->info('All customers already have unique codes!');

            return 0;
        }

        $this->info("Found {$customers->count()} customers without unique codes. Generating...");

        foreach ($customers as $customer) {
            $customer->unique_code = Customer::generateUniqueCode();
            $customer->save();

            $this->line("✓ {$customer->name} ({$customer->member_code}) -> ".substr($customer->unique_code, 0, 6).'******');
        }

        $this->info("✅ Done! Generated unique codes for {$customers->count()} customers.");

        return 0;
    }
}
