<?php

declare(strict_types=1);

namespace Domain\Exception;

use Exception;

class InsufficientFundsException extends Exception
{
    protected $message = 'Insufficient funds to complete the transaction.';
}
