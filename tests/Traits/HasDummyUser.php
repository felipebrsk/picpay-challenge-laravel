<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyUser
{
    use HasDummyCustomer;
    use HasDummyShopkeeper;

    /**
     * Create a new dummy user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createDummyUser(array $data = []): User
    {
        return User::factory()->create($data);
    }

    /**
     * Create new dummy users.
     *
     * @param int $times
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createDummyUsers(int $times, array $data = []): Collection
    {
        return User::factory($times)->create($data);
    }

    /**
     * Create a dummy user customer.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createDummyUserCustomer(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $user->userable()->associate($this->createDummyCustomer())->save();

        return $user;
    }

    /**
     * Create a dummy user shopkeeper.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createDummyUserShopkeeper(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $user->userable()->associate($this->createDummyShopkeeper())->save();

        return $user;
    }

    /**
     * Acting as dummy customer.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function actingAsDummyCustomer(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $user->userable()->associate($this->createDummyCustomer())->save();

        $this->actingAs($user);

        return $user;
    }

    /**
     * Acting as dummy shopkeeper.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function actingAsDummyShopkeeper(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $user->userable()->associate($this->createDummyShopkeeper())->save();

        $this->actingAs($user);

        return $user;
    }
}
