<?php

namespace Tests\Feature\Http\Auth;

use Tests\TestCase;
use Tests\Traits\HasDummyUser;

class LoginTest extends TestCase
{
    use HasDummyUser;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private $user;

    /**
     * Setup new environment tests.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);
    }

    /**
     * Test if can login with valid user.
     *
     * @return void
     */
    public function test_if_can_login_with_valid_user(): void
    {
        $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk();
    }

    /**
     * Test if can't login with invalid credentials.
     *
     * @return void
     */
    public function test_if_cant_login_with_invalid_credentials(): void
    {
        $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'admin12345',
        ])->assertUnauthorized()
            ->assertSee('Failed to login, verify your credentials and try again later!');
    }

    /**
     * Test if can respond with bearer token if credentials are valid.
     *
     * @return void
     */
    public function test_if_can_respond_with_bearer_token_if_credentials_are_valid(): void
    {
        $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk()->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    /**
     * Check if JWT can validate generated token.
     *
     * @return void
     */
    public function test_if_JWT_can_validate_generated_token(): void
    {
        $this->assertFalse(jwt()->check());

        $this->assertNull(jwt()->getToken());

        $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk()->json('token');

        $this->assertTrue(jwt()->check());

        $this->assertNotNull(jwt()->getToken());
    }
}
