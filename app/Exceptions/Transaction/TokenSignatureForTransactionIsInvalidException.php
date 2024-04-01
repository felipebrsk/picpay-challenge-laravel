<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\BadRequestException;

class TokenSignatureForTransactionIsInvalidException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The token signature for this transaction is invalid, expired or does not exists. Please, try again later!';
}
