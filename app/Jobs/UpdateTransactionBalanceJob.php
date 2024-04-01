<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{SerializesModels, InteractsWithQueue};
use App\Exceptions\Transaction\UnsupportedTransactionTypeException;

class UpdateTransactionBalanceJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * The related transaction.
     *
     * @var \App\Models\Transaction
     */
    public $transaction;

    /**
     * The type of transaction.
     *
     * @var int
     */
    public $type;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Transaction $transaction
     * @param int $type
     * @return void
     */
    public function __construct(Transaction $transaction, int $type)
    {
        $this->afterCommit();

        $this->transaction = $transaction;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $payee = userService()->findOrFail($this->transaction->to_id);
        $payer = userService()->findOrFail($this->transaction->from_id);

        if ($this->type === Transaction::NORMAL_TRANSACTION_TYPE_ID) {
            walletService()->add($payee, $this->transaction->amount);
            walletService()->subtract($payer, $this->transaction->amount);

            $this->transaction->update([
                'status' => 'approved',
            ]);

            return;
        } elseif ($this->type === Transaction::CHARGEBACK_TRANSACTION_TYPE_ID) {
            walletService()->add($payer, $this->transaction->amount);
            walletService()->subtract($payee, $this->transaction->amount);

            $this->transaction->update([
                'status' => 'canceled',
            ]);

            return;
        }

        throw new UnsupportedTransactionTypeException();
    }
}
