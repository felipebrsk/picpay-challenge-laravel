<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\{Customer, Shopkeeper, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'admin1234',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * @return self
     */
    public function configure(): self
    {
        return $this->afterCreating(function (User $user) {
            $userType = fake()->randomElement([User::CUSTOMER_TYPE, User::SHOPKEEEPER_TYPE]);

            if ($userType === User::CUSTOMER_TYPE) {
                $userable = Customer::create([
                    'document_type' => $type = fake()->randomElement(['cpf', 'cnpj']),
                    'document_number' => $type === 'cpf' ? fake()->cpf(false) : fake()->cnpj(),
                ]);
            } elseif ($userType === User::SHOPKEEEPER_TYPE) {
                $userable = Shopkeeper::create([
                    'document_type' => $type = fake()->randomElement(['cpf', 'cnpj']),
                    'document_number' => $type === 'cpf' ? fake()->cpf(false) : fake()->cnpj(),
                ]);
            }

            $user->userable()->associate($userable)->save();
        });
    }
}
