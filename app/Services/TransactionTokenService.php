<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Repositories\TransactionTokenRepository;
use App\Models\{Shopkeeper, Transaction, TransactionToken};
use App\Exceptions\Transaction\ShopkeepersCantCreateATransactionException;
use App\Exceptions\TransactionToken\{
    InvalidPayeeException,
    TransactionTokenAlreadyExistsException,
};

class TransactionTokenService extends AbstractService
{
    /**
     * The transaction token repository.
     *
     * @var \App\Repositories\TransactionTokenRepository
     */
    protected $repository = TransactionTokenRepository::class;

    /**
     * Find the transaction token by to_id and from_id.
     *
     * @param \App\Models\Transaction
     * @return \App\Models\TransactionToken
     */
    public function findByTransaction(Transaction $transaction): TransactionToken
    {
        return $this->repository->findByTransaction($transaction);
    }

    /**
     * Create a new transaction token.
     *
     * @param array $data
     * @return \App\Models\TransactionToken
     */
    public function create(array $data): TransactionToken
    {
        $user = Auth::user();

        $data['from_id'] = $user->id;

        $this->assertCanCreate($data, $user);

        $token = $this->repository->create($data);

        return $token;
    }

    /**
     * Check exists a transaction token for given data.
     *
     * @param array $data
     * @return bool
     */
    public function checkExistsForUsers(array $data): bool
    {
        return $this->repository->checkExistsForUsers(
            $data['to_id'],
            $data['from_id'],
        );
    }

    /**
     * Assert can create a new transaction token.
     *
     * @param array $data
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    private function assertCanCreate(array $data, Authenticatable $user): void
    {
        if ($user->isType(Shopkeeper::class)) {
            throw new ShopkeepersCantCreateATransactionException();
        }
        if ($this->checkExistsForUsers($data)) {
            throw new TransactionTokenAlreadyExistsException();
        }
        if ($data['to_id'] === $data['from_id']) {
            throw new InvalidPayeeException();
        }
    }
}
