<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\ExactApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;

final class SimpleApiProvider implements ApiInterface
{
    /**
     * @throws UnableToGetRatesException
     */
    public function getRates(): RatesCollectionInterface
    {
        try {
            return RatesCollection::make(
                json_decode(file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates'), true)
            );
        } catch (\Throwable $e) {
            throw new UnableToGetRatesException($e);
        }
    }
}
