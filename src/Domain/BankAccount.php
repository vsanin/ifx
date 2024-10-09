<?php

declare(strict_types=1);

namespace Domain;

use Domain\DTOs\Payment;
use Domain\Enum\Currency;
use Domain\Exception\ExceededDailyTransactionsException;
use Domain\Exception\InsufficientFundsException;
use Domain\Exception\InvalidCurrencyException;
use Domain\Interfaces\BankAccountInterface;

class BankAccount implements BankAccountInterface
{
    private Currency $currency;
    private float $balance = 0.0;
    private int $dailyTransactions = 0;
    private float $transactionFee;
    private int $maxDailyTransactions;

    private const float DEFAULT_TRANSACTION_FEE = 0.5;
    private const int DEFAULT_MAX_DAILY_TRANSACTIONS = 3;

    public function __construct(
        Currency $currency,
        float $transactionFee = self::DEFAULT_TRANSACTION_FEE,
        int $maxDailyTransactions = self::DEFAULT_MAX_DAILY_TRANSACTIONS
    ) {
        $this->currency = $currency;
        $this->transactionFee = $transactionFee;
        $this->maxDailyTransactions = $maxDailyTransactions;
    }

    /**
     * @throws InvalidCurrencyException
     */
    public function credit(Payment $payment): bool
    {
        if ($payment->currency !== $this->currency) {
            throw new InvalidCurrencyException();
        }

        $this->balance += $payment->amount;

        return true;
    }

    /**
     * @throws InvalidCurrencyException
     * @throws InsufficientFundsException
     * @throws ExceededDailyTransactionsException
     */
    public function debit(Payment $payment): bool
    {
        if ($payment->currency !== $this->currency) {
            throw new InvalidCurrencyException();
        }

        $totalAmount = $payment->amount + $payment->amount * ($this->transactionFee / 100);

        if ($this->balance < $totalAmount) {
            throw new InsufficientFundsException();
        }

        if ($this->dailyTransactions >= $this->maxDailyTransactions) {
            throw new ExceededDailyTransactionsException();
        }

        $this->balance -= $totalAmount;
        $this->dailyTransactions++;

        return true;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
