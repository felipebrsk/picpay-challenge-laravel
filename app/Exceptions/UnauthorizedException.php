<?php

namespace App\Exceptions;

class UnauthorizedException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 401;
}
