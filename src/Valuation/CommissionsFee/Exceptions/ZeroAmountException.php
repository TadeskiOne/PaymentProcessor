<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Exceptions;

class ZeroAmountException extends AbstractFeesCalcException
{
    public $code = 0;
    public $message = 'Transaction amount can not be 0';
}
