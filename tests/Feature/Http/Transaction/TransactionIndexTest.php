<?php

namespace Tests\Feature\Http\Transaction;

use Tests\TestCase;
use Illuminate\Support\Number;
use Tests\Traits\{HasDummyTransaction, HasDummyUser};

class TransactionIndexTest extends TestCase
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
     * Test if can get the transactions route.
     *
     * @return void
     */
    public function test_if_can_get_the_transactions_route(): void
    {
        $this->getJson(route('transactions.index'))->assertOk();
    }

    /**
     * Test if can get all the user transactions.
     *
     * @return void
     */
    public function test_if_can_get_all_the_transactions(): void
    {
        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyTransaction([
            'to_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(1, 'data');

        $this->createDummyTransaction([
            'from_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(2, 'data');

        $this->createDummyTransactions(4);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(2, 'data');
    }

    /**
     * Test if can get the correct transactions json structure.
     *
     * @return void
     */
    public function test_if_can_get_the_correct_transactions_json_structure(): void
    {
        $f = $this->createDummyTransaction([
            'to_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'status',
                    'created_at',
                    'from' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'userable' => [
                            'id',
                            'document_type',
                            'document_number',
                        ],
                    ],
                ],
            ],
        ]);

        $f->delete();

        $this->createDummyTransaction([
            'from_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'status',
                    'created_at',
                    'to' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'userable' => [
                            'id',
                            'document_type',
                            'document_number',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get the correct transactions json data.
     *
     * @return void
     */
    public function test_if_can_get_the_correct_transactions_json_data(): void
    {
        $f = $this->createDummyTransaction([
            'to_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $f->id,
                    'amount' => Number::currency($f->amount),
                    'status' => $f->status,
                    'created_at' => $f->created_at->toISOString(),
                    'from' => [
                        'id' => $f->from->id,
                        'name' => $f->from->name,
                        'email' => $f->from->email,
                        'created_at' => $f->from->created_at->toISOString(),
                        'userable' => [
                            'id' => $f->from->userable->id,
                            'document_type' => $f->from->userable->document_type,
                            'document_number' => $f->from->userable->document_number,
                        ],
                    ],
                ],
            ],
        ]);

        $f->delete();

        $f = $this->createDummyTransaction([
            'from_id' => $this->user->id,
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $f->id,
                    'amount' => Number::currency($f->amount),
                    'status' => $f->status,
                    'created_at' => $f->created_at->toISOString(),
                    'to' => [
                        'id' => $f->to->id,
                        'name' => $f->to->name,
                        'email' => $f->to->email,
                        'created_at' => $f->to->created_at->toISOString(),
                        'userable' => [
                            'id' => $f->to->userable->id,
                            'document_type' => $f->to->userable->document_type,
                            'document_number' => $f->to->userable->document_number,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
