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
     * `member_code` and `unique_code` are intentionally omitted: the model's
     * `creating` hook generates both. Duplicating that logic here used to require a
     * static counter that drifted between tests in the same PHP process.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'joined_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
