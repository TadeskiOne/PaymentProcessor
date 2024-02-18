<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\Definitions;

interface ApiInterface
{
    public function getRates(): RatesCollectionInterface;
}
