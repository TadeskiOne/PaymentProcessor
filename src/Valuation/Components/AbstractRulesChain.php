<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Components;

use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\Definitions\RuleInterface;

abstract class AbstractRulesChain
{
    protected readonly \ArrayObject $rules;

    public function __construct()
    {
        $this->rules = new \ArrayObject([]);
    }

    public function addRule(RuleInterface $rule): static
    {
        $this->rules->append($rule);

        return $this;
    }

    abstract public function handle(TransactionImmutableInterface $transaction): mixed;
}
