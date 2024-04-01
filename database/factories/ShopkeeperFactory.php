<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shopkeeper>
 */
class ShopkeeperFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type' => $type = fake()->randomElement(['cpf', 'cnpj']),
            'document_number' => $type === 'cpf' ? fake()->cpf(false) : fake()->cnpj(false),
        ];
    }
}
