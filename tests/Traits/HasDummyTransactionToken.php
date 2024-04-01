<?php

namespace Tests\Traits;

use App\Models\TransactionToken;

trait HasDummyTransactionToken
{
    /**
     * Create a new dummy transaction token.
     *
     * @param array $data
     * @return \App\Models\TransactionToken
     */
    public function createDummyTransactionToken(array $data = []): TransactionToken
    {
        return TransactionToken::factory()->create($data);
    }
}
