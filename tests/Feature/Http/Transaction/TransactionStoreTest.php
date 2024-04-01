<?php

namespace Tests\Feature\Http\Transaction;

use App\Jobs\UpdateTransactionBalanceJob;
use App\Mail\InvoiceMail;
use App\Models\Transaction;
use App\Notifications\TransactionNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Number;
use Tests\TestCase;
use Tests\Traits\{
    HasDummyUser,
    HasDummyTransaction,
    HasDummyTransactionToken,
};

class TransactionStoreTest extends TestCase
{
    use HasDummyUser;
    use HasDummyTransaction;
    use HasDummyTransactionToken;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private $user;

    /**
     * The dummy receiver.
     *
     * @var \App\Models\User
     */
    private $receiver;

    /**
     * The dummy transaction token.
     *
     * @var \App\Models\TransactionToken
     */
    private $transactionToken;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyCustomer();
        $this->receiver = $this->createDummyUserShopkeeper();
        $this->transactionToken = $this->createDummyTransactionToken([
            'from_id' => $this->user->id,
            'to_id' => $this->receiver->id,
        ]);
        $this->user->wallet()->update([
            'balance' => 500,
        ]);
    }

    /**
     * The dummy payload.
     *
     * @return array<string, mixed>
     */
    private function getDummyPayload(): array
    {
        return [
            'to_id' => $this->receiver->id,
            'token' => $this->transactionToken->token,
            'amount' => fake()->numberBetween(100, 500),
        ];
    }

    /**
     * Test if can't create a new transaction without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_without_payload(): void
    {
        $this->postJson(route('transactions.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('transactions.store'))
            ->assertUnprocessable()
            ->assertInvalid(['token', 'to_id', 'amount']);
    }

    /**
     * Test if can throw correct invalid json message.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_message(): void
    {
        $this->postJson(route('transactions.store'))
            ->assertUnprocessable()
            ->assertInvalid(['token', 'to_id', 'amount'])
            ->assertSee('The token field is required. (and 2 more errors)');
    }

    /**
     * Test if can't create a new transaction if user is shopkeeper.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_if_user_is_shopkeeper(): void
    {
        $this->user = $this->actingAsDummyShopkeeper();

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertForbidden()
            ->assertSee('Shopkeepers cant realize a transaction, only receive!');
    }

    /**
     * Test if can't create a new transaction if user balance is lower than transaction amount.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_if_user_balance_is_lower_than_transaction_amount(): void
    {
        $this->user->wallet()->update([
            'balance' => 0,
        ]);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertPaymentRequired()
            ->assertSee('Your balance is lower than the amount of transaction, impossible to proceed!');
    }

    /**
     * Test if can't create a new transaction if token is expired.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_if_token_is_expired(): void
    {
        $this->transactionToken->update([
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertBadRequest()
            ->assertSee('The token signature for this transaction is invalid, expired or does not exists. Please, try again later!');
    }

    /**
     * Test if can't create a new transaction if token is deleted.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_if_token_is_deleted(): void
    {
        $this->transactionToken->delete();

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertBadRequest()
            ->assertSee('The token signature for this transaction is invalid, expired or does not exists. Please, try again later!');
    }

    /**
     * Test if can't create a new transaction if token is invalid.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_if_token_is_invalid(): void
    {
        $this->postJson(route('transactions.store'), [
            'to_id' => $this->receiver->id,
            'token' => 'invalid',
            'amount' => fake()->numberBetween(100, 500),
        ])->assertUnprocessable()
            ->assertInvalid(['token'])
            ->assertSee('The selected token is invalid.');
    }

    /**
     * Test if can't create a new transaction with same amount and for same receiver for the last 30 minutes.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_with_same_and_for_same_receiver_amount_for_the_last_30_minutes(): void
    {
        $this->user->wallet()->update([
            'balance' => 1000,
        ]);

        $this->postJson(route('transactions.store'), $data = $this->getDummyPayload())->assertCreated();

        $this->transactionToken = $this->createDummyTransactionToken([
            'from_id' => $this->user->id,
            'to_id' => $this->receiver->id,
        ]);

        $newData = [
            'amount' => $data['amount'],
            'to_id' => $this->receiver->id,
            'token' => $this->transactionToken->token,
        ];

        $this->postJson(route('transactions.store'), $newData)
            ->assertConflict()
            ->assertSee('The transaction is duplicated. Please, wait until try to create a new transaction for the same user.');

        Transaction::where('to_id', $this->receiver->id)->where('from_id', $this->user->id)->update([
            'created_at' => Carbon::now()->subMinutes(29),
        ]);

        $this->postJson(route('transactions.store'), $newData)
            ->assertConflict()
            ->assertSee('The transaction is duplicated. Please, wait until try to create a new transaction for the same user.');

        Transaction::where('to_id', $this->receiver->id)->where('from_id', $this->user->id)->update([
            'created_at' => Carbon::now()->subMinutes(31),
        ]);

        $this->postJson(route('transactions.store'), $newData)->assertCreated();
    }

    /**
     * Test if can't create a new transaction for the same receiver for the last 10 minutes.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_for_the_same_receiver_for_the_last_10_minutes(): void
    {
        $this->user->wallet()->update([
            'balance' => 1000,
        ]);

        $this->postJson(route('transactions.store'), $data = $this->getDummyPayload())->assertCreated();

        $this->transactionToken = $this->createDummyTransactionToken([
            'from_id' => $this->user->id,
            'to_id' => $this->receiver->id,
        ]);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertConflict()
            ->assertSee('The transaction is duplicated. Please, wait until try to create a new transaction for the same user.');

        Transaction::where('to_id', $this->receiver->id)->where('from_id', $this->user->id)->update([
            'created_at' => Carbon::now()->subMinutes(9),
        ]);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())
            ->assertConflict()
            ->assertSee('The transaction is duplicated. Please, wait until try to create a new transaction for the same user.');

        Transaction::where('to_id', $this->receiver->id)->where('from_id', $this->user->id)->update([
            'created_at' => Carbon::now()->subMinutes(11),
        ]);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated();
    }

    /**
     * Test if can create a new transaction with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_new_transaction_with_valid_payload(): void
    {
        $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated();
    }

    /**
     * Test if can save the transaction in database.
     *
     * @return void
     */
    public function test_if_can_save_the_transaction_in_database(): void
    {
        $this->postJson(route('transactions.store'), $data = $this->getDummyPayload())->assertCreated();

        $this->assertDatabaseHas('transactions', [
            'from_id' => $this->user->id,
            'to_id' => $this->receiver->id,
            'amount' => $data['amount'],
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'created_at',
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->postJson(route('transactions.store'), $data = $this->getDummyPayload())->assertCreated()->assertJson([
            'data' => [
                'amount' => Number::currency($data['amount']),
            ],
        ]);
    }

    /**
     * Test if can senb the notification for both users of transaction.
     *
     * @return void
     */
    public function test_if_can_send_the_notification_for_both_users_of_transaction(): void
    {
        Notification::fake();

        $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated();

        Notification::assertSentTo(
            [
                $this->user,
                $this->receiver,
            ],
            TransactionNotification::class,
            1,
        );
    }

    /**
     * Test if can dispatch the update transaction user wallets.
     *
     * @return void
     */
    public function test_if_can_dispatch_the_update_transaction_user_wallets(): void
    {
        Bus::fake();

        $id = $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated()->json('data')['id'];

        Bus::assertDispatched(UpdateTransactionBalanceJob::class, function (UpdateTransactionBalanceJob $updateTransactionBalanceJob) use ($id) {
            return $updateTransactionBalanceJob->transaction->id === $id &&
                $updateTransactionBalanceJob->type === Transaction::NORMAL_TRANSACTION_TYPE_ID;
        });
    }

    /**
     * Test if can queue the email for both users of transaction.
     *
     * @return void
     */
    public function test_if_can_queue_the_email_for_both_users_of_transaction(): void
    {
        Mail::fake();

        $id = $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated()->json('data')['id'];

        Mail::assertQueued(
            InvoiceMail::class,
            function (InvoiceMail $invoiceMail) use ($id) {
                return $invoiceMail->hasTo([$this->user->email, $this->receiver->email]) &&
                    $invoiceMail->hasSubject('Invoice') &&
                    $invoiceMail->transaction->id === $id;
            },
        );
    }

    /**
     * Test if can invalidate a token on transaction successfull.
     *
     * @return void
     */
    public function test_if_can_invalidate_a_token_on_transaction_successfull(): void
    {
        $this->assertNotSoftDeleted($this->transactionToken);

        $this->postJson(route('transactions.store'), $this->getDummyPayload())->assertCreated();

        $this->assertSoftDeleted($this->transactionToken);
    }
}
