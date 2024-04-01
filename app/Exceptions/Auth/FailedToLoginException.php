<?php

namespace App\Exceptions\Auth;

use App\Exceptions\UnauthorizedException;

class FailedToLoginException extends UnauthorizedException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Failed to login, verify your credentials and try again later!';
}
