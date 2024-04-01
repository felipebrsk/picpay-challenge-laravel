<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\ForbiddenException;

class TransactionDoesntBelongsToUserException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given transaction does not belongs to user, can not proceed!';
}
