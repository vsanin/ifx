<?php

declare(strict_types=1);

namespace Domain\Exception;

use Exception;

class ExceededDailyTransactionsException extends Exception
{
    protected $message = 'Exceeded maximum number of daily transactions.';
}
