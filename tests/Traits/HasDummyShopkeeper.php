<?php

namespace Tests\Traits;

use App\Models\Shopkeeper;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyShopkeeper
{
    /**
     * Create a new dummy shopkeeper.
     *
     * @param array $data
     * @return \App\Models\Shopkeeper
     */
    public function createDummyShopkeeper(array $data = []): Shopkeeper
    {
        return Shopkeeper::factory()->create($data);
    }

    /**
     * Create new dummy shopkeepers.
     *
     * @param int $times
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createDummyShopkeepers(int $times, array $data = []): Collection
    {
        return Shopkeeper::factory($times)->create($data);
    }
}
