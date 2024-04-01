<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'to_id' => User::factory()->create(),
            'from_id' => User::factory()->create(),
            'status' => fake()->randomElement(['created', 'canceled', 'approved']),
            'amount' => fake()->numberBetween(1111111, 9999999),
        ];
    }
}
