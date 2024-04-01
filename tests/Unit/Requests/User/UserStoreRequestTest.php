<?php

namespace Tests\Unit\Requests\User;

use Tests\TestCase;
use Tests\Traits\TestUnitRequests;
use App\Http\Requests\User\UserStoreRequest;

class UserStoreRequestTest extends TestCase
{
    use TestUnitRequests;

    /**
     * Get the transaction store request.
     *
     * @return string
     */
    public function request(): string
    {
        return UserStoreRequest::class;
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_name_validation(): void
    {
        $this->assertFalse($this->validateFields(['name' => '']));
        $this->assertFalse($this->validateFields(['name' => 1]));

        $this->assertTrue($this->validateFields(['name' => fake()->name()]));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_email_validation(): void
    {
        $this->assertFalse($this->validateFields(['email' => '']));
        $this->assertFalse($this->validateFields(['email' => 1]));
        $this->assertFalse($this->validateFields(['email' => '1']));

        $this->assertTrue($this->validateFields(['email' => 'valid@gmail.com']));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_type_validation(): void
    {
        $this->assertFalse($this->validateFields(['type' => '']));
        $this->assertFalse($this->validateFields(['type' => 1]));
        $this->assertFalse($this->validateFields(['type' => '1']));

        $this->assertTrue($this->validateFields(['type' => 'customer']));
        $this->assertTrue($this->validateFields(['type' => 'shopkeeper']));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_document_number_validation(): void
    {
        $this->assertFalse($this->validateFields(['document_number' => '']));
        $this->assertFalse($this->validateFields(['document_number' => 1]));
        $this->assertFalse($this->validateFields(['document_number' => '1']));

        $this->assertTrue($this->validateFields(['document_number' => fake()->cpf(false)]));
        $this->assertTrue($this->validateFields(['document_number' => fake()->cnpj(false)]));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_password_validation(): void
    {
        $this->assertFalse($this->validateFields(['password' => '', 'password_confirmation' => '']));
        $this->assertFalse($this->validateFields(['password' => 1, 'password_confirmation' => 1]));
        $this->assertFalse($this->validateFields(['password' => '1', 'password_confirmation' => '1']));
        $this->assertFalse($this->validateFields(['password' => 'PNnsj8923!@j$', 'password_confirmation' => 'unmatchedPass']));

        $this->assertTrue($this->validateFields(['password' => 'PNnsj8923!@j$', 'password_confirmation' => 'PNnsj8923!@j$']));
    }
}
