<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\ExactApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\RateInterface;
use PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;

final class RatesCollection implements RatesCollectionInterface
{
    private RateInterface|null $baseRate = null;

    private function __construct(
        private readonly \ArrayObject $rates = new \ArrayObject([])
    ) {
    }

    public static function make(array $rawRates = []): self
    {
        $rates = new self(new \ArrayObject());
        $rates->initCollectionFromRaw($rawRates['rates'] ?? []);
        $rates->baseRate = new Rate($rawRates['base'] ?? getenv('BASE_CURRENCY'), 1);

        return $rates;
    }

    public function add(string $key, float $value): void
    {
        $this->rates->offsetSet($key, $value);
    }

    public function getRates(): \ArrayAccess
    {
        return $this->rates;
    }

    public function getBaseRate(): RateInterface|null
    {
        return $this->baseRate;
    }

    private function initCollectionFromRaw(array $rawRates): void
    {
        foreach ($rawRates as $title => $value) {
            $this->add(strtoupper($title), $value);
        }
    }
}
