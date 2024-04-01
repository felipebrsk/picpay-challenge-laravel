<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use App\Models\{Transaction, TransactionToken};

class TransactionTokenRepository extends AbstractRepository
{
    /**
     * The transaction token model.
     *
     * @var \App\Models\TransactionToken
     */
    protected $model = TransactionToken::class;

    /**
     * Find the transaction token by to_id and from_id.
     *
     * @param array $data
     * @return \App\Models\TransactionToken
     */
    public function findByTransaction(Transaction $transaction): TransactionToken
    {
        return $this->model::query()
            ->where('to_id', $transaction->to_id)
            ->where('from_id', $transaction->from_id)
            ->firstOrFail();
    }

    /**
     * Check if exists for given params.
     *
     * @param mixed $to_id
     * @param mixed $from_id
     * @return bool
     */
    public function checkExistsForUsers(
        mixed $to_id,
        mixed $from_id,
    ): bool {
        $exists = $this->model::query()
            ->where('to_id', $to_id)
            ->where('from_id', $from_id)
            ->where('expires_at', '>', Carbon::now())
            ->exists();

        if ($exists) {
            return true;
        }

        return false;
    }
}
