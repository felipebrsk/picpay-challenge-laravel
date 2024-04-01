<?php

namespace Tests\Feature\Http\Transaction;

use Tests\TestCase;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Bus;
use App\Jobs\UpdateTransactionBalanceJob;
use Tests\Traits\{HasDummyTransaction, HasDummyUser};

class TransactionCancelTest extends TestCase
{
    use HasDummyUser;
    use HasDummyTransaction;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private $user;

    /**
     * The dummy transaction.
     *
     * @var \App\Models\Transaction
     */
    private $transaction;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyCustomer();
        $this->transaction = $this->createDummyTransaction([
            'from_id' => $this->user->id,
        ]);
    }

    /**
     * Test if can't cancel a transaction that doesn't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_cancel_a_transaction_that_doesnt_belongs_to_user(): void
    {
        $this->putJson(route('transactions.cancel', $this->createDummyTransaction()))
            ->assertForbidden()
            ->assertSee('The given transaction does not belongs to user, can not proceed!');
    }

    /**
     * Test if can't cancel a transaction that is already canceled.
     *
     * @return void
     */
    public function test_if_cant_cancel_a_transaction_that_is_already_canceled(): void
    {
        $this->transaction->update([
            'status' => TransactionStatus::Canceled->value,
        ]);

        $this->putJson(route('transactions.cancel', $this->transaction))
            ->assertConflict()
            ->assertSee('The transaction is already canceled!');
    }

    /**
     * Test if can't cancel a transaction that is not approved yet.
     *
     * @return void
     */
    public function test_if_cant_cancel_a_transaction_that_is_not_approved_yet(): void
    {
        $this->transaction->update([
            'status' => TransactionStatus::Created->value,
        ]);

        $this->putJson(route('transactions.cancel', $this->transaction))
            ->assertBadRequest()
            ->assertSee('Please, wait for the transaction conclusion before cancel.');
    }

    /**
     * Test if can't cancel old transactions.
     *
     * @return void
     */
    public function test_if_cant_cancel_old_transactions(): void
    {
        $this->transaction = $this->createDummyTransaction([
            'status' => TransactionStatus::Approved->value,
            'created_at' => Carbon::now()->subDay(),
            'from_id' => $this->user->id,
        ]);

        $this->putJson(route('transactions.cancel', $this->transaction))
            ->assertForbidden()
            ->assertSee('Transactions can only be canceled on the same day they were created.');
    }

    /**
     * Test if can dispatch the update transaction user wallets.
     *
     * @return void
     */
    public function test_if_can_dispatch_the_update_transaction_user_wallets(): void
    {
        Bus::fake();

        $this->transaction->update([
            'status' => TransactionStatus::Approved->value,
        ]);

        $this->putJson(route('transactions.cancel', $this->transaction))->assertOk();

        Bus::assertDispatched(UpdateTransactionBalanceJob::class, function (UpdateTransactionBalanceJob $updateTransactionBalanceJob) {
            return $updateTransactionBalanceJob->transaction->id === $this->transaction->id &&
                $updateTransactionBalanceJob->type === Transaction::CHARGEBACK_TRANSACTION_TYPE_ID;
        });
    }
}
