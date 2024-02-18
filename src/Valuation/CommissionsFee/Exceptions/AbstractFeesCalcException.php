<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Exceptions;

use PaymentProcessor\Entities\TransactionImmutableInterface;

abstract class AbstractFeesCalcException extends \LogicException
{
    public function __construct(public readonly TransactionImmutableInterface $transaction, ?\Throwable $previous = null)
    {
        parent::__construct($this->message, $this->code, $previous);
    }
}
