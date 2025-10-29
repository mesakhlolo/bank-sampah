<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo customers...');

        // Create specific demo customers
        Customer::create([
            'member_code' => 'CUST-0001',
            'name' => 'Budi Setiawan',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'joined_at' => '2025-09-01',
        ]);

        Customer::create([
            'member_code' => 'CUST-0002',
            'name' => 'Siti Aminah',
            'phone' => '082145678901',
            'address' => 'Jl. Sudirman No. 456, Jakarta',
            'joined_at' => '2025-09-15',
        ]);

        $this->command->info('Creating random customers...');

        // Create additional random customers (50 customers total for better testing)
        Customer::factory()->count(48)->create();

        $this->command->info('✓ Total customers created: '.Customer::count());
    }
}
