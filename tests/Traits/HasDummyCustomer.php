<?php

namespace Tests\Traits;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyCustomer
{
    /**
     * Create a new dummy customer.
     *
     * @param array $data
     * @return \App\Models\Customer
     */
    public function createDummyCustomer(array $data = []): Customer
    {
        return Customer::factory()->create($data);
    }

    /**
     * Create new dummy customers.
     *
     * @param int $times
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createDummyCustomers(int $times, array $data = []): Collection
    {
        return Customer::factory($times)->create($data);
    }
}
