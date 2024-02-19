<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;
use PaymentProcessor\Valuation\Definitions\RuleAmountTypeInterface;
use PaymentProcessor\Valuation\Definitions\RuleOperationTypeInterface;

abstract class AbstractRule implements CommissionFeeRuleInterface
{
    protected float $amount = 0.00;

    protected FeeAmountType $amountType;

    protected FeeOperationType $feeOperationType;

    public function __construct(protected readonly RulesMath $operations)
    {
    }

    public function applyTo(TransactionImmutableInterface $transaction): TransactionImmutableInterface
    {
        $this->validateTransaction($transaction);

        return $transaction->modify(
            amount: $this->operations->ceil($this->operations->applyOperation($transaction->getAmount(), $this))
        );
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmountType(RuleAmountTypeInterface $amountType): static
    {
        $this->amountType = $amountType;

        return $this;
    }

    public function getAmountType(): FeeAmountType
    {
        return $this->amountType;
    }

    public function setOperationType(RuleOperationTypeInterface $feeOperationType): static
    {
        $this->feeOperationType = $feeOperationType;

        return $this;
    }

    public function getOperationType(): FeeOperationType
    {
        return $this->feeOperationType;
    }

    public function getOptionsNames(): array
    {
        return [];
    }

    public function setOptions(mixed ...$options): static
    {
        return $this;
    }

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
