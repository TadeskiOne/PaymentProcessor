<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Exceptions;

class UndefinedCurrencyException extends AbstractFeesCalcException
{
    public $code = 2;
    public $message = 'Undefined currency';
}
