<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\ForbiddenException;

class CantCancelOldTransactionsException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Transactions can only be canceled on the same day they were created.';
}
