<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Components;

use PaymentProcessor\Entities\TransactionImmutableInterface;

interface TransactionsRegistryInterface
{
    public function defineInitiator(TransactionImmutableInterface $transaction);

    public function defineWeekPeriod(TransactionImmutableInterface $transaction);

    public function hasWeeklyRegistry(): bool;

    public function getWeeklyRegistry(): \ArrayAccess;
}
