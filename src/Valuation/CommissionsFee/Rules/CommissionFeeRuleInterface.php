<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\Definitions\RuleInterface;

/**
 * Interface CommissionFeeRuleInterface.
 *
 * This interface represents a commission fee rule that can be applied to a transaction.
 *
 * @see     RuleInterface
 */
interface CommissionFeeRuleInterface extends RuleInterface
{
    /**
     * Applies this object's modifications to a given transaction.
     *
     * @param TransactionImmutableInterface $transaction the transaction to apply modifications to
     *
     * @return TransactionImmutableInterface the modified transaction
     */
    public function applyTo(TransactionImmutableInterface $transaction): TransactionImmutableInterface;
}
