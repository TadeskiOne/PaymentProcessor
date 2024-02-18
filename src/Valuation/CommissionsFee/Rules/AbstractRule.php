<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;

/**
 * AbstractRule is an abstract class that implements the CommissionFeeRuleInterface.
 *
 * It provides a base implementation for applying commission fees to transactions.
 */
abstract class AbstractRule implements CommissionFeeRuleInterface
{
    public function __construct(protected readonly RulesMath $operations)
    {
    }

    /**
     * Applies the operation to a transaction.
     *
     * @param TransactionImmutableInterface $transaction the transaction to apply the operation to
     *
     * @return TransactionImmutableInterface the modified transaction after applying the operation
     *
     * @throws NegativeAmountException if the transaction amount is negative after applying the operation
     * @throws ZeroAmountException     if the transaction amount is zero after applying the operation
     */
    public function applyTo(TransactionImmutableInterface $transaction): TransactionImmutableInterface
    {
        $this->validateTransaction($transaction);

        return $transaction->modify(
            amount: $this->operations->ceil($this->operations->applyOperation($transaction->getAmount(), $this))
        );
    }

    /**
     * Validates a transaction.
     *
     * @param TransactionImmutableInterface $transaction the transaction to validate
     *
     * @throws NegativeAmountException if the transaction amount is negative
     * @throws ZeroAmountException     if the transaction amount is zero
     */
    protected function validateTransaction(TransactionImmutableInterface $transaction): void
    {
        if ($transaction->getAmount() < 0) {
            throw new NegativeAmountException($transaction);
        }

        if ($transaction->getAmount() === 0.00) {
            throw new ZeroAmountException($transaction);
        }
    }
}
