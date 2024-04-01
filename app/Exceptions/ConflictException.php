<?php

namespace App\Exceptions;

class ConflictException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 409;
}
