<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\Transaction;
use App\Jobs\UpdateTransactionBalanceJob;
use Illuminate\Support\Facades\{Bus, Queue};
use Tests\Traits\{HasDummyTransaction, HasDummyUser};

class UpdateTransactionBalanceJobTest extends TestCase
{
    use HasDummyUser;
    use HasDummyTransaction;

    /**
     * Test if the job can be dispatched.
     *
     * @return void
     */
    public function test_if_the_job_can_be_dispatched(): void
    {
        Bus::fake();

        Bus::dispatch(new UpdateTransactionBalanceJob($this->createDummyTransaction(), Transaction::NORMAL_TRANSACTION_TYPE_ID));

        Bus::assertDispatched(UpdateTransactionBalanceJob::class, 1);
    }

    /**
     * Test if the job can be queued.
     *
     * @return void
     */
    public function test_if_the_job_can_be_queued(): void
    {
        Queue::fake();

        Bus::dispatch(new UpdateTransactionBalanceJob($this->createDummyTransaction(), Transaction::NORMAL_TRANSACTION_TYPE_ID));

        Queue::assertPushed(UpdateTransactionBalanceJob::class, 1);
    }

    /**
     * Test if update the wallets of given users.
     *
     * @return void
     */
    public function test_if_can_update_the_user_wallets_on_job_run(): void
    {
        $payer = $this->createDummyUserCustomer();
        $payee = $this->createDummyUserShopkeeper();

        $payer->wallet()->update([
            'balance' => $prevCustomerBalance = 500,
        ]);

        $payee->wallet()->update([
            'balance' => $prevShopkeeperBalance = 0,
        ]);

        $transaction = $this->createDummyTransaction([
            'to_id' => $payee->id,
            'from_id' => $payer->id,
            'amount' => 100,
        ]);

        Bus::dispatch(new UpdateTransactionBalanceJob($transaction, Transaction::NORMAL_TRANSACTION_TYPE_ID));

        $this->assertEquals(
            $payer->wallet->balance,
            $prevCustomerBalance - $transaction->amount,
        );

        $this->assertEquals(
            $payee->wallet->balance,
            $prevShopkeeperBalance + $transaction->amount,
        );
    }

    /**
     * Test if can approve the transaction on job run.
     *
     * @return void
     */
    public function test_if_can_approve_the_transaction_on_job_run(): void
    {
        $payer = $this->createDummyUserCustomer();
        $payee = $this->createDummyUserShopkeeper();

        $transaction = $this->createDummyTransaction([
            'to_id' => $payee->id,
            'status' => 'created',
            'from_id' => $payer->id,
        ]);

        $this->assertTrue($transaction->status === 'created');

        Bus::dispatch(new UpdateTransactionBalanceJob($transaction, Transaction::NORMAL_TRANSACTION_TYPE_ID));

        $transaction->refresh();

        $this->assertTrue($transaction->status === 'approved');
    }

    /**
     * Test if can chargeback the transaction if canceled.
     *
     * @return void
     */
    public function test_if_can_chargeback_the_transaction_if_canceled(): void
    {
        $payer = $this->createDummyUserCustomer();
        $payee = $this->createDummyUserShopkeeper();

        $payer->wallet()->update([
            'balance' => $prevCustomerBalance = 500,
        ]);

        $payee->wallet()->update([
            'balance' => $prevShopkeeperBalance = 0,
        ]);

        $transaction = $this->createDummyTransaction([
            'to_id' => $payee->id,
            'from_id' => $payer->id,
            'amount' => 100,
        ]);

        Bus::dispatch(new UpdateTransactionBalanceJob($transaction, Transaction::NORMAL_TRANSACTION_TYPE_ID));

        $this->assertEquals(
            $payer->wallet->balance,
            $prevCustomerBalance - $transaction->amount,
        );

        $this->assertEquals(
            $payee->wallet->balance,
            $prevShopkeeperBalance + $transaction->amount,
        );

        Bus::dispatch(new UpdateTransactionBalanceJob($transaction, Transaction::CHARGEBACK_TRANSACTION_TYPE_ID));

        $payee->refresh();
        $payer->refresh();

        $this->assertEquals(
            $payer->wallet->balance,
            $prevCustomerBalance,
        );

        $this->assertEquals(
            $payee->wallet->balance,
            $prevShopkeeperBalance,
        );
    }
}
