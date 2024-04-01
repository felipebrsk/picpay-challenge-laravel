<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Customer, Shopkeeper};

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $customerUser = User::create([
            'name' => 'Customer',
            'password' => 'Admin1234!',
            'email' => 'customer@gmail.com',
        ]);

        $customer = Customer::create([
            'document_type' => 'cpf',
            'document_number' => fake()->cpf(false),
        ]);

        $customerUser->userable()->associate($customer)->save();

        $customerUser->wallet()->update([
            'balance' => 200000000,
        ]);

        $shopkeeperUser = User::create([
            'name' => 'Shopkeeper',
            'password' => 'Admin1234!',
            'email' => 'shopkeeper@gmail.com',
        ]);

        $shopkeeper = Shopkeeper::create([
            'document_type' => 'cnpj',
            'document_number' => fake()->cnpj(false),
        ]);

        $shopkeeperUser->userable()->associate($shopkeeper)->save();
    }
}
