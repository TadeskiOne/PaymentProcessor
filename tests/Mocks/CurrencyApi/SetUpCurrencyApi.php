<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Mocks\CurrencyApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Components\CurrencyApi\Definitions\RateInterface;
use PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

trait SetUpCurrencyApi
{
    use ProphecyTrait;

    public function setUpApi(): ObjectProphecy
    {
        $rates = json_decode(file_get_contents(__DIR__.'/mock_rates.json'), true);
        $ratesCollection = $this->prophesize(RatesCollectionInterface::class);
        $baseRate = $this->prophesize(RateInterface::class);

        $baseRate->getCurrencyTitle()
                 ->willReturn($rates['base']);
        $baseRate->getExchangeValue()
                 ->willReturn(1.0);

        $ratesCollection->getBaseRate()
                        ->willReturn($baseRate->reveal());
        $ratesCollection->getRates()
                        ->willReturn(new \ArrayObject($rates['rates']));

        $apiInterface = $this->prophesize(ApiInterface::class);

        $apiInterface->getRates()
                     ->willReturn($ratesCollection->reveal());

        return $apiInterface;
    }
}
