<?php

namespace App\Exceptions\TransactionToken;

use App\Exceptions\BadRequestException;

class InvalidPayeeException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The payee is invalid. Please, check the data and try again!';
}
