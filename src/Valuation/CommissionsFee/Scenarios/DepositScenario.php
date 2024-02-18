<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Rules\DepositRule;

final class DepositScenario extends AbstractScenario
{
    public function applyTo(TransactionImmutableInterface $transaction): ?TransactionImmutableInterface
    {
        if ($transaction->getType() !== TransactionType::deposit) {
            return null;
        }

        return $this->collector->collectRule(DepositRule::class)->applyTo($transaction);
    }
}
