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

        // Create additional random customers
        Customer::factory()->count(8)->create();
    }
}
