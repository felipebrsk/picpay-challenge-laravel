<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\BadRequestException;

class AwaitTransactionConclusionToCancelException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Please, wait for the transaction conclusion before cancel.';
}
