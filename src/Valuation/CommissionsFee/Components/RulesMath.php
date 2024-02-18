<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Components;

use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use PaymentProcessor\Valuation\CommissionsFee\Rules\CommissionFeeRuleInterface;

class RulesMath
{
    public function applyOperation(float $amount, CommissionFeeRuleInterface $rule): float
    {
        return match ($rule->getOperationType()) {
            FeeOperationType::multiply => $this->multiply($amount, $rule),
            FeeOperationType::divide => $this->divide($amount, $rule),
            FeeOperationType::add => $this->add($amount, $rule),
            FeeOperationType::reduce => $this->reduce($amount, $rule),
        };
    }

    public function multiply(float $amount, CommissionFeeRuleInterface $rule): float
    {
        return match ($rule->getAmountType()) {
            FeeAmountType::percentage => $amount * ($rule->getAmount() / 100),
            FeeAmountType::amount => $amount * $rule->getAmount()
        };
    }

    public function divide(float $amount, CommissionFeeRuleInterface $rule): float
    {
        return match ($rule->getAmountType()) {
            FeeAmountType::percentage => $amount / ($rule->getAmount() / 100),
            FeeAmountType::amount => $amount / $rule->getAmount()
        };
    }

    public function add(float $amount, CommissionFeeRuleInterface $rule): float
    {
        return match ($rule->getAmountType()) {
            FeeAmountType::percentage => $amount / (1 - ($rule->getAmount() / 100)),
            FeeAmountType::amount => $amount + $rule->getAmount()
        };
    }

    public function reduce(float $amount, CommissionFeeRuleInterface $rule): float
    {
        return match ($rule->getAmountType()) {
            FeeAmountType::percentage => $amount - ($amount * ($rule->getAmount() / 100)),
            FeeAmountType::amount => $amount - $rule->getAmount()
        };
    }

    public function ceil(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }
}
