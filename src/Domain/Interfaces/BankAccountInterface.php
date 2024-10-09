<?php

declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\DTOs\Payment;
use Domain\Enum\Currency;

interface BankAccountInterface
{
    public function credit(Payment $payment): bool;
    public function debit(Payment $payment): bool;
    public function getBalance(): float;
    public function getCurrency(): Currency;
}
