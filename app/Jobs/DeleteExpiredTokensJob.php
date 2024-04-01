<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Models\TransactionToken;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{SerializesModels, InteractsWithQueue};

class DeleteExpiredTokensJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        TransactionToken::where('expires_at', '<=', Carbon::now())->get()->each(function (TransactionToken $transactionToken) {
            $transactionToken->delete();
        });
    }
}
