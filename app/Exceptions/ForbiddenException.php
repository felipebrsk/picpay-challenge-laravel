<?php

namespace App\Exceptions;

class ForbiddenException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 403;
}
