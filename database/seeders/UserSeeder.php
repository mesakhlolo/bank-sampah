<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@banksampah.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Operator user
        User::create([
            'name' => 'Ibu Rina (Operator)',
            'email' => 'operator@banksampah.test',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);
    }
}
