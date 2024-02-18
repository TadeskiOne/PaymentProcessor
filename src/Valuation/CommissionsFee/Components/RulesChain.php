<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Components;

use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Rules\CommissionFeeRuleInterface;
use PaymentProcessor\Valuation\Components\AbstractRulesChain;

final class RulesChain extends AbstractRulesChain
{
    public function handle(TransactionImmutableInterface $transaction): TransactionImmutableInterface
    {
        $processedTransaction = $transaction;

        /** @var CommissionFeeRuleInterface $rule */
        foreach ($this->rules as $rule) {
            $processedTransaction = $rule->applyTo($processedTransaction);
        }

        return $processedTransaction;
    }
}
