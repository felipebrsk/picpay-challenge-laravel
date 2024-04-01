<?php

namespace Tests\Feature\Http\Auth;

use Tests\TestCase;
use Tests\Traits\HasDummyUser;
use Illuminate\Support\Number;

class MeTest extends TestCase
{
    use HasDummyUser;

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
     * Test if can access the me route.
     *
     * @return void
     */
    public function test_if_can_access_the_me_route(): void
    {
        $this->getJson(route('me'))->assertOk();
    }

    /**
     * Test if can get correctly json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correctly_json_attributes_count(): void
    {
        $this->getJson(route('me'))->assertOk()->assertJsonCount(6, 'data');
    }

    /**
     * Test if can get correctly json structure.
     *
     * @return void
     */
    public function test_if_can_get_correctly_json_structure(): void
    {
        $this->getJson(route('me'))->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'userable' => [
                    'id',
                    'document_type',
                    'document_number',
                ],
                'wallet' => [
                    'id',
                    'balance',
                    'created_at',
                    'updated_at',
                ],
            ]
        ]);
    }

    /**
     * Test if can get correctly user.
     *
     * @return void
     */
    public function test_if_can_get_correctly_user(): void
    {
        $this->getJson(route('me'))->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'created_at' => $this->user->created_at->toIsoString(),
                'userable' => [
                    'id' => $this->user->userable_id,
                    'document_type' => $this->user->userable->document_type,
                    'document_number' => $this->user->userable->document_number,
                ],
                'wallet' => [
                    'id' => $this->user->wallet->id,
                    'balance' => Number::currency($this->user->wallet->balance),
                    'created_at' => $this->user->wallet->created_at->toIsoString(),
                    'updated_at' => $this->user->wallet->updated_at->toIsoString(),
                ],
            ]
        ]);
    }
}
