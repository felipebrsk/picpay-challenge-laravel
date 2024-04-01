<?php

namespace Tests\Feature\Http\TransactionToken;

use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\{HasDummyUser, HasDummyTransactionToken};

class TransactionTokenStoreTest extends TestCase
{
    use HasDummyUser;
    use HasDummyTransactionToken;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyCustomer();
    }

    /**
     * Get valid dummy payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'to_id' => $this->createDummyUserShopkeeper()->id,
        ];
    }

    /**
     * Test if shopkeepers can't create new tokens.
     *
     * @return void
     */
    public function test_if_shopkeepers_cant_create_new_tokens(): void
    {
        $this->actingAsDummyShopkeeper();

        $this->postJson(route('transactions.token'), $this->getValidPayload())
            ->assertForbidden()
            ->assertSee('Shopkeepers cant realize a transaction, only receive!');
    }

    /**
     * Test if can't create a new transaction token without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_new_transaction_token_without_payload(): void
    {
        $this->postJson(route('transactions.token'))->assertUnprocessable();
    }

    /**
     * Test if can show correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_show_correct_invalid_json_keys(): void
    {
        $this->postJson(route('transactions.token'))
            ->assertUnprocessable()
            ->assertInvalid(['to_id']);
    }

    /**
     * Test if can show correct invalid json message.
     *
     * @return void
     */
    public function test_if_can_show_correct_invalid_json_message(): void
    {
        $this->postJson(route('transactions.token'))
            ->assertUnprocessable()
            ->assertInvalid(['to_id'])
            ->assertSee('The to id field is required.');
    }

    /**
     * Test if can't create a token for myself.
     *
     * @return void
     */
    public function test_if_cant_create_a_token_for_myself(): void
    {
        $this->postJson(route('transactions.token'), [
            'to_id' => $this->user->id,
        ])->assertBadRequest()
            ->assertSee('The payee is invalid. Please, check the data and try again!');
    }

    /**
     * Test if can't duplicate the token if is not expired.
     *
     * @return void
     */
    public function test_if_cant_duplicate_the_token_if_is_not_expired(): void
    {
        $data = $this->getValidPayload();

        $this->postJson(route('transactions.token'), $data)->assertCreated();

        $this->postJson(route('transactions.token'), $data)
            ->assertConflict()
            ->assertSee('Please, wait until try again!');
    }

    /**
     * Test if can create a new transaction token.
     *
     * @return void
     */
    public function test_if_can_create_a_new_transaction_token(): void
    {
        $this->postJson(route('transactions.token'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can create a new transaction token if old is already expired.
     *
     * @return void
     */
    public function test_if_can_create_a_new_transaction_token_if_old_is_already_expired(): void
    {
        $data = $this->getValidPayload();

        $token = $this->createDummyTransactionToken($data + [
            'from_id' => $this->user->id,
        ]);

        $token->update([
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        $this->postJson(route('transactions.token'), $data)->assertCreated();
    }

    /**
     * Test if can save the token in database.
     *
     * @return void
     */
    public function test_if_can_save_the_token_in_database(): void
    {
        $this->postJson(route('transactions.token'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('transaction_tokens', [
            'to_id' => $data['to_id'],
            'from_id' => $this->user->id,
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->postJson(route('transactions.token'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'token',
                'expires_at',
                'created_at',
            ],
        ]);
    }
}
