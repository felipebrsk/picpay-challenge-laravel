<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository extends AbstractRepository
{
    /**
     * The transaction model.
     *
     * @var \App\Models\Transaction
     */
    protected $model = Transaction::class;

    /**
     * All for user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allForUser(): Collection
    {
        $userId = Auth::id();

        return $this->model::with(
            'to',
            'from',
        )->where('from_id', $userId)->orWhere('to_id', $userId)->get();
    }

    /**
     * Check if exists a recent transaction for the same users.
     *
     * @param array $data
     * @return bool
     */
    public function shouldBlockRecentTransaction(array $data): bool
    {
        $recentTransaction = $this->model::query()
            ->whereIn('status', [TransactionStatus::Approved->value, TransactionStatus::Created->value])
            ->where('to_id', $data['to_id'])
            ->where('from_id', $data['from_id'])
            ->where('amount', $data['amount'])
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->exists();

        $recentAnyTransaction = $this->model::query()
            ->whereIn('status', [TransactionStatus::Approved->value, TransactionStatus::Created->value])
            ->where('to_id', $data['to_id'])
            ->where('from_id', $data['from_id'])
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->exists();

        if ($recentTransaction || $recentAnyTransaction) {
            return true;
        }

        return false;
    }
}
