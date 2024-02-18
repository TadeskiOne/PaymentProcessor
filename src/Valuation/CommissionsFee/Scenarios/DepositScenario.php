<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use PaymentProcessor\Valuation\CommissionsFee\Rules\SimpleFeeRule;

final class DepositScenario extends AbstractScenario
{
    public function applyTo(TransactionImmutableInterface $transaction): ?TransactionImmutableInterface
    {
        if ($transaction->getType() !== TransactionType::deposit) {
            return null;
        }

        return $this->collector->collectRule(SimpleFeeRule::class)
            ->setAmount(0.03)
            ->setAmountType(FeeAmountType::percentage)
            ->setOperationType(FeeOperationType::multiply)
            ->applyTo($transaction);
    }
}
