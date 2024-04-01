<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Enums\TransactionStatus;
use Illuminate\Support\{Number, Carbon};
use App\Models\{Shopkeeper, Transaction};
use App\Jobs\UpdateTransactionBalanceJob;
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{Mail, Auth};
use App\Notifications\TransactionNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Exceptions\TransactionToken\InvalidPayeeException;
use App\Exceptions\Transaction\{
    DuplicatedTransactionException,
    CantCancelOldTransactionsException,
    TransactionIsAlreadyCanceledException,
    TransactionDoesntBelongsToUserException,
    ShopkeepersCantCreateATransactionException,
    AwaitTransactionConclusionToCancelException,
    TokenSignatureForTransactionIsInvalidException,
    UserBalanceIsLowerThanTransactionAmountException,
};

class TransactionService extends AbstractService
{
    /**
     * The transaction repository.
     *
     * @var \App\Repositories\TransactionRepository
     */
    protected $repository = TransactionRepository::class;

    /**
     * Get all transactions for user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allForUser(): Collection
    {
        return $this->repository->allForUser();
    }

    /**
     * Create a new transaction.
     *
     * @param array $data
     * @return \App\Models\Transaction
     */
    public function create(array $data): Transaction
    {
        $user = Auth::user()->load('userable', 'wallet');

        $data['from_id'] = $user->id;

        $this->assertCanCreate($data, $user);

        $transaction = $this->repository->create($data);

        $this->invalidateToken($transaction);

        $this->updateTransactionBalances($transaction, Transaction::NORMAL_TRANSACTION_TYPE_ID);

        $this->notify($transaction);

        $this->mail($transaction);

        return $transaction;
    }

    /**
     * Cancel the transaction.
     *
     * @param string $id
     * @return \App\Models\Transaction
     */
    public function cancel(string $id): Transaction
    {
        $user = Auth::user();

        $transaction = $this->findOrFail($id);

        $this->assertCanCancel($transaction, $user);

        $this->updateTransactionBalances($transaction, Transaction::CHARGEBACK_TRANSACTION_TYPE_ID);

        return $transaction;
    }

    /**
     * Assert can create a new transaction.
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
        if ($data['from_id'] === $data['to_id']) {
            throw new InvalidPayeeException();
        }
        if ($user->wallet->balance < $data['amount']) {
            throw new UserBalanceIsLowerThanTransactionAmountException();
        }
        if (!transactionTokenService()->checkExistsForUsers($data)) {
            throw new TokenSignatureForTransactionIsInvalidException();
        }
        if ($this->repository->shouldBlockRecentTransaction($data)) {
            throw new DuplicatedTransactionException();
        }
    }

    /**
     * Assert can cancel.
     *
     * @param \App\Models\Transaction $transaction
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    private function assertCanCancel(Transaction $transaction, Authenticatable $user): void
    {
        if ($transaction->from_id != $user->id) {
            throw new TransactionDoesntBelongsToUserException();
        }
        if ($transaction->status === TransactionStatus::Canceled->value) {
            throw new TransactionIsAlreadyCanceledException();
        }
        if ($transaction->status !== TransactionStatus::Approved->value) {
            throw new AwaitTransactionConclusionToCancelException();
        }
        if (Carbon::parse($transaction->created_at)->lessThan(Carbon::today())) {
            throw new CantCancelOldTransactionsException();
        }
    }

    /**
     * Update the wallet balances.
     *
     * @param \App\Models\Transaction $transaction
     * @param string $type
     * @return void
     */
    private function updateTransactionBalances(Transaction $transaction, string $type): void
    {
        UpdateTransactionBalanceJob::dispatch($transaction, $type);
    }

    /**
     * Notify given user of successfull transaction.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    private function notify(Transaction $transaction): void
    {
        $transaction->load('to', 'from');

        $fromName = $transaction->from->name;
        $toName = $transaction->to->name;
        $amount = Number::currency($transaction->amount);

        $forPayee = [
            'title' => 'New received transaction!',
            'data' => "You've received a new transaction from $fromName of $amount!",
            'icon' => 'FaDollarSign',
            'actionUrl' => "https://front.com/transactions/$transaction->id"
        ];

        $forPayer = [
            'title' => 'New sent transaction!',
            'data' => "You've sent a new transaction to $toName of $amount!",
            'icon' => 'FaDollarSign',
            'actionUrl' => "https://front.com/transactions/$transaction->id"
        ];

        $transaction->to->notify(new TransactionNotification($forPayee));
        $transaction->from->notify(new TransactionNotification($forPayer));
    }

    /**
     * Mail the given users of successfull transaction.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    private function mail(Transaction $transaction): void
    {
        Mail::send(new InvoiceMail($transaction));
    }

    /**
     * Invalidate token after successfull transaction.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    private function invalidateToken(Transaction $transaction): void
    {
        $token = transactionTokenService()->findByTransaction($transaction);

        $token->delete();
    }
}
