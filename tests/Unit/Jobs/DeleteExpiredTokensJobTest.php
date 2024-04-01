<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use App\Jobs\DeleteExpiredTokensJob;
use Tests\Traits\HasDummyTransactionToken;
use Illuminate\Support\Facades\{Bus, Queue};

class DeleteExpiredTokensJobTest extends TestCase
{
    use HasDummyTransactionToken;

    /**
     * Test if the job can be dispatched.
     *
     * @return void
     */
    public function test_if_the_job_can_be_dispatched(): void
    {
        Bus::fake();

        Bus::dispatch(new DeleteExpiredTokensJob());

        Bus::assertDispatched(DeleteExpiredTokensJob::class, 1);
    }

    /**
     * Test if the job can be queued.
     *
     * @return void
     */
    public function test_if_the_job_can_be_queued(): void
    {
        Queue::fake();

        Bus::dispatch(new DeleteExpiredTokensJob());

        Queue::assertPushed(DeleteExpiredTokensJob::class, 1);
    }

    /**
     * Test if can ignore non expired tokens.
     *
     * @return void
     */
    public function test_if_can_ignore_non_expired_tokens(): void
    {
        $token = $this->createDummyTransactionToken();

        $this->assertNotSoftDeleted($token);

        Bus::dispatch(new DeleteExpiredTokensJob());

        $this->assertNotSoftDeleted($token);

        $token->update([
            'expires_at' => Carbon::now()->addMinute(),
        ]);

        Bus::dispatch(new DeleteExpiredTokensJob());

        $this->assertNotSoftDeleted($token);
    }

    /**
     * Test if can delete the expired tokens from database.
     *
     * @return void
     */
    public function test_if_can_delete_the_expired_tokens_from_database(): void
    {
        $token = $this->createDummyTransactionToken();

        $this->assertNotSoftDeleted($token);

        Bus::dispatch(new DeleteExpiredTokensJob());

        $this->assertNotSoftDeleted($token);

        $token->update([
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        Bus::dispatch(new DeleteExpiredTokensJob());

        $this->assertSoftDeleted($token);
    }
}
