<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\Definitions;

interface RatesCollectionInterface
{
    public function add(string $key, float $value): void;

    public function getRates(): \ArrayObject;

    public function getBaseRate(): RateInterface|null;
}
