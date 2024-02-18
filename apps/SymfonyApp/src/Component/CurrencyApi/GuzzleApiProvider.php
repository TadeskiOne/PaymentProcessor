<?php

declare(strict_types=1);

namespace PaymentProcessor\Apps\Symfony\Component\CurrencyApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;
use PaymentProcessor\Components\CurrencyApi\ExactApi\RatesCollection;
use PaymentProcessor\Components\CurrencyApi\ExactApi\UnableToGetRatesException;
use GuzzleHttp\Client;

final class GuzzleApiProvider implements ApiInterface
{
    public function __construct(private readonly Client $httpClient)
    {
    }

    /**
     * @throws UnableToGetRatesException
     */
    public function getRates(): RatesCollectionInterface
    {
        try {
            $response = $this->httpClient->get(getenv('CURRENCY_API_PATH'));

            return RatesCollection::make(
                json_decode($response->getBody()->getContents(), true)
            );
        } catch (\Throwable $e) {
            throw new UnableToGetRatesException($e);
        }
    }
}
