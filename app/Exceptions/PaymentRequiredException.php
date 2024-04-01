<?php

namespace App\Exceptions;

class PaymentRequiredException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 402;
}
