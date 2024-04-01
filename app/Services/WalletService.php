<?php

namespace App\Services;

use App\Models\{User, Transaction};

class WalletService
{
    /**
     * Subtract an amount for the given user.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function subtract(User $user, int $amount): void
    {
        $user->updateBalance(
            Transaction::SUBTRACTION_CONST_ID,
            $amount,
        );
    }

    /**
     * Add an amount for the given user.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function add(User $user, int $amount): void
    {
        $user->updateBalance(
            Transaction::ADDITION_CONST_ID,
            $amount,
        );
    }
}
