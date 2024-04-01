<?php

namespace Tests\Unit\Requests\Transaction;

use Tests\TestCase;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use Tests\Traits\{HasDummyTransactionToken, HasDummyUser, TestUnitRequests};

class TransactionStoreRequestTest extends TestCase
{
    use HasDummyUser;
    use TestUnitRequests;
    use HasDummyTransactionToken;

    /**
     * Get the transaction store request.
     *
     * @return string
     */
    public function request(): string
    {
        return TransactionStoreRequest::class;
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_amount_validation(): void
    {
        $this->assertFalse($this->validateFields(['amount' => '']));
        $this->assertFalse($this->validateFields(['amount' => 0]));

        $this->assertTrue($this->validateFields(['amount' => '81923']));
        $this->assertTrue($this->validateFields(['amount' => fake()->numberBetween(11111111, 9999999)]));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_to_id_validation(): void
    {
        $this->assertFalse($this->validateFields(['to_id' => '']));
        $this->assertFalse($this->validateFields(['to_id' => 99]));
        $this->assertFalse($this->validateFields(['to_id' => '99']));

        $this->assertTrue($this->validateFields(['to_id' => $this->createDummyUser()->id]));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_token_validation(): void
    {
        $this->assertFalse($this->validateFields(['token' => '']));
        $this->assertFalse($this->validateFields(['token' => 1]));
        $this->assertFalse($this->validateFields(['token' => '1']));

        $this->assertTrue($this->validateFields(['token' => $this->createDummyTransactionToken()->token]));
    }
}
