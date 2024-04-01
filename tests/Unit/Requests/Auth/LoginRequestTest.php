<?php

namespace Tests\Unit\Requests\Auth;

use Tests\TestCase;
use Tests\Traits\TestUnitRequests;
use App\Http\Requests\Auth\LoginRequest;

class LoginRequestTest extends TestCase
{
    use TestUnitRequests;

    /**
     * Get the transaction store request.
     *
     * @return string
     */
    public function request(): string
    {
        return LoginRequest::class;
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_email_validation(): void
    {
        $this->assertFalse($this->validateFields(['email' => '']));
        $this->assertFalse($this->validateFields(['email' => 0]));
        $this->assertFalse($this->validateFields(['email' => '81923']));

        $this->assertTrue($this->validateFields(['email' => 'valid@gmail.com']));
    }

    /**
     * Test the to id validation.
     *
     * @return void
     */
    public function test_the_password_validation(): void
    {
        $this->assertFalse($this->validateFields(['password' => '']));
        $this->assertFalse($this->validateFields(['password' => 99]));

        $this->assertTrue($this->validateFields(['password' => '99']));
        $this->assertTrue($this->validateFields(['password' => fake()->sentence()]));
    }
}
