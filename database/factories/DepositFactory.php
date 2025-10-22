<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', 'now');
        $dateStr = $date->format('Ymd');
        $sequence = fake()->numberBetween(1, 9999);

        return [
            'code' => sprintf('DEP-%s-%04d', $dateStr, $sequence),
            'customer_id' => \App\Models\Customer::factory(),
            'created_by' => \App\Models\User::factory(),
            'transaction_date' => $date,
            'total_amount' => fake()->numberBetween(5000, 100000),
            'status' => 'final',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
