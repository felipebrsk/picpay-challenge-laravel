<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\ConflictException;

class DuplicatedTransactionException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The transaction is duplicated. Please, wait until try to create a new transaction for the same user.';
}
