<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\CurrencyApi\ExactApi;

use PaymentProcessor\Components\CurrencyApi\Definitions\FailedRequestExceptionInterface;

final class UnableToGetRatesException extends \Exception implements FailedRequestExceptionInterface
{
    public function __construct(\Throwable $previous)
    {
        parent::__construct('Unable to get rates', 0, $previous);
    }
}
