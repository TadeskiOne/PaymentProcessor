<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesChain;
use PaymentProcessor\Valuation\CommissionsFee\Rules\BusinessWithdrawRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\PrivateWithdrawRule;

final class WithdrawScenario extends AbstractScenario
{
    public function applyTo(TransactionImmutableInterface $transaction): ?TransactionImmutableInterface
    {
        if ($transaction->getType() !== TransactionType::withdraw) {
            return null;
        }

        return match ($transaction->getInitiatorType()) {
            InitiatorType::private => (new RulesChain())
                ->addRule($this->collector->collectRule(PrivateWithdrawRule::class))
                ->handle($transaction),
            InitiatorType::business => (new RulesChain())
                ->addRule($this->collector->collectRule(BusinessWithdrawRule::class))
                ->handle($transaction)
        };
    }
}
