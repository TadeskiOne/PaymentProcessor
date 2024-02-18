<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesChain;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use PaymentProcessor\Valuation\CommissionsFee\Rules\HasFreeFromFeesRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\SimpleFeeRule;

final class WithdrawScenario extends AbstractScenario
{
    public function applyTo(TransactionImmutableInterface $transaction): ?TransactionImmutableInterface
    {
        if ($transaction->getType() !== TransactionType::withdraw) {
            return null;
        }

        return match ($transaction->getInitiatorType()) {
            InitiatorType::private => (new RulesChain())
                ->addRule(
                    $this->collector->collectRule(HasFreeFromFeesRule::class)
                    ->setAmount(0.3)
                    ->setAmountType(FeeAmountType::percentage)
                    ->setOperationType(FeeOperationType::multiply)
                    ->setOptions(weeklyAmountRestriction: 1000, freeFromFeesCount: 3)
                )
                ->handle($transaction),
            InitiatorType::business => (new RulesChain())
                ->addRule(
                    $this->collector->collectRule(SimpleFeeRule::class)
                    ->setAmount(0.5)
                    ->setAmountType(FeeAmountType::percentage)
                    ->setOperationType(FeeOperationType::multiply)
                )
                ->handle($transaction)
        };
    }
}
