<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WD-'.now()->format('Ymd').'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => \App\Models\Customer::factory(),
            'amount' => fake()->numberBetween(10000, 500000),
            'transaction_date' => now(),
            'note' => fake()->optional()->sentence(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
