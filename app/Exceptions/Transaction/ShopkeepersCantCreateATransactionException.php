<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\ForbiddenException;

class ShopkeepersCantCreateATransactionException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Shopkeepers cant realize a transaction, only receive!';
}
