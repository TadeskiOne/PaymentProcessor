<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Exceptions;

class NegativeAmountException extends AbstractFeesCalcException
{
    public $code = 1;
    public $message = 'Transaction amount can not less then 0';
}
