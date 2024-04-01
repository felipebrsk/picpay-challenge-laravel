<?php

namespace Tests\Traits;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTransaction
{
    /**
     * Create a new dummy transaction.
     *
     * @param array $data
     * @return \App\Models\Transaction
     */
    public function createDummyTransaction(array $data = []): Transaction
    {
        return Transaction::factory()->create($data);
    }

    /**
     * Create new dummy transactions.
     *
     * @param int $times
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createDummyTransactions(int $times, array $data = []): Collection
    {
        return Transaction::factory($times)->create($data);
    }
}
