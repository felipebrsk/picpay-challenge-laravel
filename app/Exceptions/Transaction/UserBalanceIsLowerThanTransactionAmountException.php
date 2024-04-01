<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\PaymentRequiredException;

class UserBalanceIsLowerThanTransactionAmountException extends PaymentRequiredException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Your balance is lower than the amount of transaction, impossible to proceed!';
}
