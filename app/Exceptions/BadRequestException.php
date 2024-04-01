<?php

namespace App\Exceptions;

class BadRequestException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 400;
}
