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
            $apiPath = getenv('CURRENCY_API_PATH') ?: base64_decode('aHR0cHM6Ly9kZXZlbG9wZXJzLnBheXNlcmEuY29tL3Rhc2tzL2FwaS9jdXJyZW5jeS1leGNoYW5nZS1yYXRlcw==');

            return RatesCollection::make(
                json_decode(file_get_contents($apiPath), true)
            );
        } catch (\Throwable $e) {
            throw new UnableToGetRatesException($e);
        }
    }
}
