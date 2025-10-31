<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
            $customer->unique_code = $this->generateUniqueCode();
            $customer->save();

            $this->line("✓ {$customer->name} ({$customer->member_code}) -> {$customer->unique_code}");
        }

        $this->info("✅ Done! Generated unique codes for {$customers->count()} customers.");

        return 0;
    }

    /**
     * Generate secure random unique code
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'BSB-'.strtoupper(Str::random(12));
        } while (Customer::where('unique_code', $code)->exists());

        return $code;
    }
}
