<?php

namespace Tests\Unit\Requests\TransactionToken;

use Tests\TestCase;
use Tests\Traits\{HasDummyUser, TestUnitRequests};
use App\Http\Requests\Transaction\TransactionStoreRequest;

class TransactionTokenStoreRequestTest extends TestCase
{
    use HasDummyUser;
    use TestUnitRequests;

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
    public function test_the_to_id_validation(): void
    {
        $this->assertFalse($this->validateFields(['to_id' => '']));
        $this->assertFalse($this->validateFields(['to_id' => 1]));
        $this->assertFalse($this->validateFields(['to_id' => '1']));

        $this->assertTrue($this->validateFields(['to_id' => $this->createDummyUser()->id]));
    }
}
