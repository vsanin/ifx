<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\BankAccount;
use Domain\DTOs\Payment;
use Domain\Enum\Currency;
use Domain\Exception\ExceededDailyTransactionsException;
use Domain\Exception\InsufficientFundsException;
use Domain\Exception\InvalidCurrencyException;
use PHPUnit\Framework\TestCase;

class BankAccountTest extends TestCase
{
    private BankAccount $bankAccount;

    protected function setUp(): void
    {
        $this->bankAccount = new BankAccount(Currency::USD);
    }

    public function testInitialBalance(): void
    {
        $this->assertEquals(0.0, $this->bankAccount->getBalance());
    }

    public function testAccountHasCurrency(): void
    {
        $this->assertEquals(Currency::USD, $this->bankAccount->getCurrency());
    }

    public function testCreditSameCurrency(): void
    {
        $payment = new Payment(100.0, Currency::USD);
        $this->bankAccount->credit($payment);
        $this->assertEquals(100.0, $this->bankAccount->getBalance());
    }

    public function testCreditDifferentCurrency(): void
    {
        $payment = new Payment(100.0, Currency::EUR);

        $this->expectException(InvalidCurrencyException::class);
        $this->bankAccount->credit($payment);
    }

    public function testDebitSameCurrency(): void
    {
        $this->bankAccount->credit(new Payment(100.0, Currency::USD));

        $payment = new Payment(50.0, Currency::USD);
        $this->bankAccount->debit($payment);

        $this->assertEquals(49.75, $this->bankAccount->getBalance()); // 50.0 + 0.25 fee
    }

    public function testDebitDifferentCurrency(): void
    {
        $this->bankAccount->credit(new Payment(100.0, Currency::USD));

        $payment = new Payment(50.0, Currency::EUR);

        $this->expectException(InvalidCurrencyException::class);
        $this->bankAccount->debit($payment);
    }

    public function testDebitInsufficientFunds(): void
    {
        $payment = new Payment(100.0, Currency::USD);

        $this->expectException(InsufficientFundsException::class);
        $this->bankAccount->debit($payment);
    }

    public function testExceededDailyTransactions(): void
    {
        $this->bankAccount->credit(new Payment(300.0, Currency::USD));

        $this->bankAccount->debit(new Payment(50.0, Currency::USD));
        $this->bankAccount->debit(new Payment(50.0, Currency::USD));
        $this->bankAccount->debit(new Payment(50.0, Currency::USD));

        $this->expectException(ExceededDailyTransactionsException::class);
        $this->bankAccount->debit(new Payment(50.0, Currency::USD)); // 4th transaction
    }

    public function testTransactionFeeCalculation(): void
    {
        $this->bankAccount->credit(new Payment(100.5, Currency::USD));

        $payment = new Payment(100.0, Currency::USD);
        $this->bankAccount->debit($payment);

        $this->assertEquals(0.0, $this->bankAccount->getBalance()); // 100.5 - 100.0 - 0.5
    }

    public function testMultipleCredits(): void
    {
        $this->bankAccount->credit(new Payment(100.0, Currency::USD));
        $this->bankAccount->credit(new Payment(50.0, Currency::USD));
        $this->assertEquals(150.0, $this->bankAccount->getBalance());
    }

    public function testNegativePaymentAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Payment(-50.0, Currency::USD);
    }

    public function testDebitExactBalance(): void
    {
        $this->bankAccount->credit(new Payment(100.5, Currency::USD));

        $payment = new Payment(100.0, Currency::USD); // Fee is 0.5, total deduction = 100.0 + 0.5
        $this->bankAccount->debit($payment);

        $this->assertEqualsWithDelta(0.0, $this->bankAccount->getBalance(), 0.00000001); // Allowable delta
    }
}
