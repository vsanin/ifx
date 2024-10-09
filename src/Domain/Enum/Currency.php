<?php

declare(strict_types=1);

namespace Domain\Enum;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case PLN = 'PLN';
}
