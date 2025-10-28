<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Using a static counter to ensure sequential numbering during factory generation
        static $counter = null;

        if ($counter === null) {
            // Initialize counter from last customer in database
            $lastCustomer = \App\Models\Customer::orderBy('id', 'desc')->first();

            if (! $lastCustomer) {
                $counter = 1;
            } else {
                // Extract number from last code
                preg_match('/(\d+)$/', $lastCustomer->member_code, $matches);
                $lastNumber = isset($matches[1]) ? (int) $matches[1] : $lastCustomer->id;
                $counter = $lastNumber + 1;
            }
        }

        // Use minimum 4 digits, but allow more if needed
        $memberCode = 'CUST-'.str_pad($counter, 4, '0', STR_PAD_LEFT);
        $counter++; // Increment for next call

        return [
            'member_code' => $memberCode,
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'joined_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
