<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Created = 'created';
    case Canceled = 'canceled';
    case Approved = 'approved';
}
