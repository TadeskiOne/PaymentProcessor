<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\Definitions;

interface RateInterface
{
    public function getCurrencyTitle(): string;

    public function getExchangeValue(): float;
}
