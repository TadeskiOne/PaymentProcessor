<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Components;

use PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\UndefinedCurrencyException;

trait CurrencyConverterTrait
{
    private readonly RatesCollectionInterface $rates;

    private function convertToBaseCurrency(TransactionImmutableInterface $transaction): float
    {
        return $transaction->getAmount() / $this->getCurrencyRate($transaction);
    }

    private function convertToOriginalCurrency(float $amount, TransactionImmutableInterface $transaction): float
    {
        return $amount * $this->getCurrencyRate($transaction);
    }

    private function getCurrencyRate(TransactionImmutableInterface $transaction)
    {
        if ($this->rates->getBaseRate()->getCurrencyTitle() === $transaction->getCurrencyTitle()) {
            return $this->rates->getBaseRate()->getExchangeValue();
        }

        if (!isset($this->rates->getRates()[$transaction->getCurrencyTitle()])) {
            throw new UndefinedCurrencyException($transaction);
        }

        return $this->rates->getRates()[$transaction->getCurrencyTitle()];
    }
}
