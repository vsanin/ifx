<?php

declare(strict_types=1);

namespace Domain\Exception;

use Exception;

class InvalidCurrencyException extends Exception
{
    protected $message = 'Invalid currency for this account.';
}
