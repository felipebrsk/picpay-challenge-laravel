<?php

namespace App\Exceptions\TransactionToken;

use App\Exceptions\ConflictException;

class TransactionTokenAlreadyExistsException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Please, wait until try again!';
}
