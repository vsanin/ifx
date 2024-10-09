<?php

declare(strict_types=1);

namespace Domain\DTOs;

use Domain\Enum\Currency;

readonly class Payment
{
    public function __construct(public float $amount, public Currency $currency)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than zero.");
        }
    }
}
