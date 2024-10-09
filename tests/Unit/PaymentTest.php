<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\DTOs\Payment;
use Domain\Enum\Currency;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function testValidPayment(): void
    {
        $payment = new Payment(100.0, Currency::USD);

        $this->assertEquals(100.0, $payment->amount);
        $this->assertEquals(Currency::USD, $payment->currency);
    }

    public function testInvalidPaymentAmountZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Amount must be greater than zero.");

        new Payment(0, Currency::USD);
    }

    public function testInvalidPaymentAmountNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Amount must be greater than zero.");

        new Payment(-100.0, Currency::USD);
    }

    public function testDifferentCurrencies(): void
    {
        $paymentUSD = new Payment(100.0, Currency::USD);
        $paymentEUR = new Payment(75.0, Currency::EUR);
        $paymentPLN = new Payment(50.0, Currency::PLN);

        $this->assertEquals(100.0, $paymentUSD->amount);
        $this->assertEquals(Currency::USD, $paymentUSD->currency);

        $this->assertEquals(75.0, $paymentEUR->amount);
        $this->assertEquals(Currency::EUR, $paymentEUR->currency);

        $this->assertEquals(50.0, $paymentPLN->amount);
        $this->assertEquals(Currency::PLN, $paymentPLN->currency);
    }
}
