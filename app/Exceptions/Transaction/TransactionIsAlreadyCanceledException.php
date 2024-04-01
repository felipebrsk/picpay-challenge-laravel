<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\ConflictException;

class TransactionIsAlreadyCanceledException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The transaction is already canceled!';
}
