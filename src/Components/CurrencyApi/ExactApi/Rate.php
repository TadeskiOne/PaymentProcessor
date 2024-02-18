<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\ExactApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\RateInterface;

final class Rate implements RateInterface
{
    public function __construct(
        private readonly string $title,
        private readonly float $exchangeValue,
    ) {
    }

    public function getCurrencyTitle(): string
    {
        return strtoupper($this->title);
    }

    public function getExchangeValue(): float
    {
        return $this->exchangeValue;
    }
}
